<?php
namespace App\Controller;

use App\Entity\Doctrine;
use App\Manager\CommandesManager;
use App\Provider\AclProvider;
use App\Provider\AppProvider;
use App\Provider\AuthProvider;
use App\Twig\TwigExtensions;
use Core\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FL\QBJSParser\Parser\Doctrine\DoctrineParser;
use FL\QBJSParser\Serializer\JsonDeserializer;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Permissions\Acl\Acl;

class CommandesController extends Controller
{
    /**
     * @var string
     */
    protected $menu_item = 'commandes';

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var Doctrine\SerieEntity[] $series
         */
        $series = $this->getRepository(Doctrine\SerieEntity::class)->findAll();
        $users = $this->getRepository(Doctrine\UserEntity::class)->findAll();
        $ilots = $this->getRepository(Doctrine\IlotEntity::class)->findAll();

        $seriesBuilder = [];
        $usersBuilder = [];

        foreach ($series as $serie) {
            $seriesBuilder[$serie->getId()] = TwigExtensions::serieLabelFilter($serie, false);
        }
        foreach ($users as $user) {
            $usersBuilder[$user->getId()] = $user->getVmeId() . " - " . (string) $user;
        }

        foreach ($ilots as $ilot) {
            $ilotsBuilder[$ilot->getId()] = (string) $ilot->getName();
        }

        $builderValues = [
            'series' => $seriesBuilder,
            'users' => $usersBuilder,
            'ilots' => $ilotsBuilder
        ];

        // recherche
        $jsonQuery = $request->getQueryParam('query', false);
        $comments_types = $this->getRepository(Doctrine\CommentTypeEntity::class)->findAll();

        // on fait bien une recherche
        if ($jsonQuery !== false) {
            // on récupère le paramètre en base64 décodé au format JSON
            $filter = base64_decode($jsonQuery);
            // on re-parse le query mais avec les bons formats de dates cette fois
            $deserializedRuleGroup = (new JsonDeserializer())->deserialize($filter);
            // on précise quel parser utiliser
            $commandesParser = new DoctrineParser(Doctrine\CommandeEntity::class,
                Doctrine\CommandeEntity::QUERYBUILDER_MAPPING,
                [
                    'processings' => Doctrine\CommandeProcessingEntity::class
                ]
            );
            $parsedRuleGroup = $commandesParser->parse($deserializedRuleGroup);

            // on créer le query DQL Doctrine (du moins on récupère celui généré)
            $qb = $this->getDoctrine()->createQuery($parsedRuleGroup->getQueryString());
            $qb->setParameters($parsedRuleGroup->getParameters());

            // on exécute la requête DQL Doctrine
            $commandes = $qb->execute();

            return $this->render($response, 'commandes.search_results', [
                'commandes' => $commandes,
                'builder_rules' => $filter,
                'comments_types' => $comments_types,
                'builder_values' => $builderValues
            ]);
        }

        $max = 5;
        $paginatorStart = $request->getQueryParam('p', 0);
        if (!is_numeric($paginatorStart)) {
            $paginatorStart = 0;
        }

        /**
         * @var Paginator|Doctrine\CommandeEntity[] $commandes
         */
        $commandes = $this->getManager(CommandesManager::class)->getPaginatorCommandes($paginatorStart, $max);

        $prev = $paginatorStart + 1;
        $next = $paginatorStart - 1;

        if ($commandes->getIterator()->count() === $commandes->count()) {
            $prev = -1;
        }

        return $this->render($response, 'commandes.lastest_commandes', [
            'commandes' => $commandes,
            'prev' => $prev,
            'next' => $next,
            'max' => $max,
            'comments_types' => $comments_types,
            'builder_values' => $builderValues
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $bill_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function detailsAction(Request $request, Response $response, int $bill_id): ResponseInterface
    {
        $commande = $this->getRepository(Doctrine\CommandeEntity::class)->find($bill_id);

        if ($commande instanceof Doctrine\CommandeEntity === false) {
            throw new NotFoundException($request, $response);
        }

        $depots = $this->getRepository(Doctrine\DepotEntity::class)->findAll();
        /**
         * @var Doctrine\IlotEntity[] $ilots
         */
        $ilots = $this->getRepository(Doctrine\IlotEntity::class)->findAll();
        $ilotsByDepots = [];

        foreach ($ilots as $ilot) {
            $ilotsByDepots[$ilot->getLocation()->getId()][] = $ilot;
        }

        return $this->render($response, 'commandes.details', [
            'commande' => $commande,
            'depots' => $depots,
            'ilotsByDepots' => $ilotsByDepots,
            'vendor' => $commande->getVendor()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $bill_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function updateAction(Request $request, Response $response, int $bill_id): ResponseInterface
    {
        $commande = $this->getRepository(Doctrine\CommandeEntity::class)->find($bill_id);

        // la commande existe bien en base de données
        if ($commande instanceof Doctrine\CommandeEntity) {
            $cas_type = $request->getParsedBodyParam('cas_type');
            $client_reference = $request->getParsedBodyParam('client_reference');
            $machine_ts = $request->getParsedBodyParam('machine_ts');
            $depart_ts = $request->getParsedBodyParam('depart_ts');
            $delivery_at = $request->getParsedBodyParam('delivery_at');
            $sous_traitant = $request->getParsedBodyParam('sous_traitant');
            $transport = $request->getParsedBodyParam('transport');

            // on modifie la commande et on capture les erreurs...
            try {
                $commande->setCasType($cas_type);
                $commande->setClientReference($client_reference);
                $commande->setMachineTs($machine_ts);
                $commande->setSousTraitant($sous_traitant);
                $commande->setTransport($transport);

                $depart_ts = \DateTime::createFromFormat('d/m/Y', $depart_ts) ?: null;
                $commande->setDepartTs($depart_ts);

                $delivery_at = \DateTime::createFromFormat('d/m/Y', $delivery_at) ?: null;
                $commande->setDeliveryAt($delivery_at);

                $errors = $this->persist($commande);
                if ($errors === null) {
                    // tout est ok
                    $code = 200;
                    $this->addMessage('success', "Les informations de la commande ont bien été modifiées");
                } else {
                    $this->addValidatorMessages($errors);
                }
            } catch (\TypeError $e) {
                $message = "Veuillez vérifier les informations saisie dans le formulaire";

                if (AppProvider::getEnv() === 'dev') {
                    $message .= " " . $e->getMessage();
                }

                $this->addMessage('warning', $message);
            }

            return $this->withRedirect($response, 'commandes.details', ['bill_id' => $bill_id]);
        }

        throw new NotFoundException($request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $charge_id
     *
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     */
    public function updateChargeAction(Request $request, Response $response, int $charge_id): ResponseInterface
    {
        $charge = $this->getRepository(Doctrine\CommandeProcessingEntity::class)->find($charge_id);

        // on a trouvé la charge demandée
        if ($charge instanceof Doctrine\CommandeProcessingEntity) {
            if ($request->isGet()) {
                return $response->withJson($charge);
            } elseif ($request->isPut()) {
                if ($charge->getBill()->isCanceled()) {
                    return $response->withJson([
                        'error' => "Cette commande ne peut pas être modifiée car elle a été annulée"
                    ], 403);
                }

                $depot_id = (int) $request->getParsedBodyParam('depot_id');
                $ilot_id = (int) $request->getParsedBodyParam('ilot_id');
                $quantity = (int) $request->getParsedBodyParam('quantity');
                $processing_at = \DateTime::createFromFormat("d/m/Y", $request->getParsedBodyParam('processing_at'));

                $depot = $this->getRepository(Doctrine\DepotEntity::class)->find($depot_id);
                $ilot = $this->getRepository(Doctrine\IlotEntity::class)->find($ilot_id);

                try {
                    $charge->setQuantity($quantity)
                        ->setDepot($depot)
                        ->setIlot($ilot)
                        ->setProcessingAt($processing_at);

                    if ($charge->getProcessingAt() > $charge->getBill()->getProcessingAt()) {
                        $charge->getBill()->setProcessingAt($charge->getProcessingAt());
                    }

                    $errors = $this->persist($charge);
                    if ($errors === null) {
                        return $response->withJson(['message' => "Les informations du prélèvement sur le l'ilôt {$charge->getIlot()} ont bien été modifiées"]);
                    } else {
                        return $response->withJson(['errors' => $errors], 400);
                    }
                } catch (\TypeError $e) {
                    $message = "Veuillez vérifier les informations saisie dans le formulaire";

                    if (AppProvider::getEnv() === 'dev') {
                        $message .= " " . $e->getMessage();
                    }

                    return $response->withJson(['error' => $message], 500);
                }
            } else {
                throw new MethodNotAllowedException($request, $response);
            }
        }

        return $response->withStatus(404);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $bill_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function cancelAction(Request $request, Response $response, int $bill_id): ResponseInterface
    {
        /**
         * @var Doctrine\CommandeEntity $commande
         */
        $commande = $this->getRepository(Doctrine\CommandeEntity::class)->find($bill_id);
        $now = (new \DateTime());

        if ($commande instanceof Doctrine\CommandeEntity === false) {
            throw new NotFoundException($request, $response);
        }

        if ($commande->getCanceledAt() instanceof \DateTime === false) {
            $message = $request->getQueryParam('m', "Annulation de la commande");

            $cancelType = (new Doctrine\CommentTypeEntity())
                ->setType('annulation_commande')
                ->setLabel("Annulation commande");

            $cancelComment = (new Doctrine\CommentEntity())
                ->setBill($commande)
                ->setAuthor($this->container->get(AuthProvider::getKey())->getIdentity())
                ->setType($cancelType)
                ->setText($message)
                ->setDate($now)
                ->setCreatedAt($now);

            $cancelComment = $this->getDoctrine()->merge($cancelComment);

            $commande->setCanceledAt($now);
            $commande->addComment($cancelComment);

            $errors = $this->persist($commande);
            if ($errors !== null) {
                foreach ($errors as $error) {
                    $this->addMessage('danger', $error->getMessage());
                }
            } else {
                $this->addMessage('success', "La commande n°{$commande->getId()} a été annulée");
            }
        }

        return $this->withRedirect($response, 'commandes.details', ['bill_id' => $commande->getId()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $serie_id
     *
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        /**
         * @var Doctrine\SerieEntity $serie
         */
        $serie = $this->getRepository(Doctrine\SerieEntity::class)->find($serie_id);

        $vendor = $this->container->auth->getIdentity();
        $today = (new \DateTime())->setTimestamp($request->getServerParam('REQUEST_TIME'));

        $bill_id = $request->getParsedBodyParam('bill_id', 0);
        $bl_id = $request->getParsedBodyParam('bl_id', 0);
        $cas_type = strip_tags($request->getParsedBodyParam('cas_type', null));
        $client_name = strip_tags($request->getParsedBodyParam('client_name', null));
        $client_ref = strip_tags($request->getParsedBodyParam('client_reference', null));
        $sous_traitant = strip_tags($request->getParsedBodyParam('sous_traitant', null));
        $transporteur = strip_tags($request->getParsedBodyParam('transporteur', null));
        $machine_ts = strip_tags($request->getParsedBodyParam('machine_ts', null));
        $depart_ts = strip_tags($request->getParsedBodyParam('depart_ts', null));
        $date_livraison = strip_tags($request->getParsedBodyParam('date_livraison', null));

        $comments = $request->getParsedBodyParam('comments', []);
        $processings = $request->getParsedBodyParam('supplies', []);

        if (empty($bill_id) || !is_array($processings)) {
            return $response->withStatus(400);
        }

        $commandeExists = $this->getRepository(Doctrine\CommandeEntity::class)->find($bill_id);
        if ($commandeExists instanceof Doctrine\CommandeEntity === false) {
            /**
             * @var Doctrine\SerieStarterEntity $starter
             */
            $starter = $this->getRepository(Doctrine\SerieStarterEntity::class)->find($bl_id);
            $today = $starter->getCreatedAt();
            if ($starter instanceof Doctrine\SerieStarterEntity === false) {
                throw new \Exception("Le N° de BL {$bl_id} n'existe pas");
            }

            try {
                $date_depart_ts = \DateTime::createFromFormat("d/m/Y", $depart_ts) ?: null;
                $date_livraison = \DateTime::createFromFormat("d/m/Y", $date_livraison) ?: null;

                $commande = (new Doctrine\CommandeEntity)
                    ->setId($bill_id)
                    ->setBlId($starter->getStarter())
                    ->setSerie($serie)
                    ->setVendor($vendor)
                    ->setCasType($cas_type)
                    ->setClientName($client_name)
                    ->setClientReference($client_ref)
                    ->setCreatedAt($today)
                    ->setTransport($transporteur)
                    ->setSousTraitant($sous_traitant)
                    ->setMachineTs($machine_ts)
                    ->setDepartTs($date_depart_ts)
                    ->setDeliveryAt($date_livraison);

                /**
                 * @var Doctrine\CommandeEntity $commande
                 */
                $commande = $this->getDoctrine()->merge($commande);

                if (!empty($comments)) {
                    foreach ($comments as $type => $commentsByType) {
                        foreach ($commentsByType as $comment) {
                            $text = $comment['text'] ?? null;
                            $date = \DateTime::createFromFormat('d/m/Y', $comment['date']);

                            /**
                             * @var Doctrine\CommentTypeEntity $commentType
                             */
                            $commentType = $this->getRepository(Doctrine\CommentTypeEntity::class)->find($type);

                            $newComment = (new Doctrine\CommentEntity())
                                ->setBill($commande)
                                ->setAuthor($commande->getVendor())
                                ->setType($commentType)
                                ->setText($text)
                                ->setCreatedAt($commande->getCreatedAt());

                            if ($date instanceof \DateTime) {
                                $newComment->setDate($date);
                            }

                            $commande->addComment($newComment);
                        }
                    }
                }

                $commande_processing = null;
                foreach ($processings as $processing) {
                    $from_depot = strip_tags($processing['from_depot'] ?? "");
                    $to_ilot = strip_tags($processing['to_ilot'] ?? "");
                    $quantity = $processing['quantity'] ?? 0;
                    $processing_at = \DateTime::createFromFormat('Y-m-d', $processing['processing_at']);

                    if (!is_numeric($from_depot) || !is_numeric($to_ilot)) {
                        throw new \TypeError("Le numéro de dépôt ou le numéro d'îlot est invalide");
                    }

                    /**
                     * @var Doctrine\DepotEntity $depot
                     */
                    $depot = $this->getRepository(Doctrine\DepotEntity::class)->find($from_depot);
                    /**
                     * @var Doctrine\IlotEntity $ilot
                     */
                    $ilot = $this->getRepository(Doctrine\IlotEntity::class)->find($to_ilot);

                    if ($depot instanceof Doctrine\DepotEntity === false) {
                        throw new \TypeError("Le dépôt saisie semble ne pas exister");
                    }
                    if ($ilot instanceof Doctrine\IlotEntity === false) {
                        throw new \TypeError("L'îlot saisie semble ne pas exister");
                    }

                    $newProcessing = (new Doctrine\CommandeProcessingEntity())
                        ->setBill($commande)
                        ->setQuantity($quantity)
                        ->setDepot($depot)
                        ->setIlot($ilot)
                        ->setProcessingAt($processing_at);

                    if ($commande_processing === null) {
                        $commande_processing = $newProcessing->getProcessingAt();
                    } elseif ($newProcessing->getProcessingAt() > $commande_processing) {
                        $commande_processing = $newProcessing->getProcessingAt();
                    }

                    $commande->addProcessing($newProcessing);
                }

                if ($commande_processing instanceof \DateTime) {
                    $commande->setProcessingAt($commande_processing);
                }

                $errors = $this->persist($commande);

                if ($errors === null) {
                    $this->getDoctrine()->remove($starter);
                    $this->getDoctrine()->flush();

                    return $response->withJson($commande, 201);
                } else {
                    return $response->withJson($errors, 400);
                }
            } catch (\TypeError $e) {
                return $response->withJson(['error' => $e->getMessage()], 400);
            }
        }

        return $response->withJson($commandeExists, 409);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $commande_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function addCommentAction(Request $request, Response $response, int $commande_id): ResponseInterface
    {
        /**
         * @var Doctrine\CommandeEntity $commande
         */
        $commande = $this->getRepository(Doctrine\CommandeEntity::class)->find($commande_id);
        if ($commande instanceof Doctrine\CommandeEntity === false) {
            throw new NotFoundException($request, $response);
        }

        $comment_type = $request->getParsedBodyParam('comment_type', null);
        $comment_text = $request->getParsedBodyParam('comment_text', null);
        $comment_date = $request->getParsedBodyParam('comment_date', new \DateTime());

        $commentType = $this->getRepository(Doctrine\CommentTypeEntity::class)->find($comment_type);

        try {
            $newComment = (new Doctrine\CommentEntity())
                ->setBill($commande)
                ->setAuthor($this->container->auth->getIdentity())
                ->setType($commentType)
                ->setText($comment_text)
                ->setDate($comment_date)
                ->setCreatedAt($comment_date);

            /**
             * @var Doctrine\CommentEntity $newComment
             */
            $newComment = $this->getDoctrine()->merge($newComment);
            $commande->addComment($newComment);

            // date de reception de la commande par le client
            if ($commande->isCanceled() === false && $newComment->getType()->getType() === "date_reception_client") {
                $commande->setReceptionAt($newComment->getCreatedAt());
            } elseif ($commande->isReceptionned() === false && $newComment->getType()->getType() === "annulation_commande") {
                $commande->setCanceledAt($newComment->getCreatedAt());
            }

            $errors = $this->persist($commande);

            if ($errors === null) {
                return $response->withJson($newComment);
            } else {
                return $response->withJson(['errors' => $this->parseValidator($errors)], 500);
            }
        } catch (\TypeError $e) {
            $message = "Veuillez vérifier les informations saisies dans le formulaire";

            if (AppProvider::getEnv() === "dev") {
                $message .= " " . $e->getMessage();
            }

            return $response->withJson(['error' => $message], 500);
        }
    }
}
