<?php
namespace App\Controller\Admin;

use App\Entity\Doctrine\DepotEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Doctrine\SerieStarterEntity;
use App\Twig\TwigExtensions;
use Core\Controller\Controller;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminGestionnaireController extends Controller
{
    protected $menu_item = 'admin.gestionnaires';

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        $series = $this->getRepository(SerieEntity::class)->findBy([], ['id' => "ASC"]);

        return $this->render($response, 'admin.gestionnaires.index', ['series' => $series]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response): ResponseInterface
    {
        // le formulaire de création a été envoyé
        if ($request->isPost()) {
            /**
             * @var int $depot_id
             */
            $depot_id = $request->getParsedBodyParam('depot_id');

            // le paramètre du dépôt a été précisé
            if ($depot_id !== null) {
                /**
                 * @var DepotEntity $depot
                 */
                $depot = $this->getRepository(DepotEntity::class)->find($depot_id);

                // on continue si le dépôt existe
                if ($depot instanceof DepotEntity) {
                    $id = (string) $request->getParsedBodyParam('id', 0);
                    $label = $request->getParsedBodyParam('label', null);
                    $starterId = $request->getParsedBodyParam('starter_id', 0);

                    // on continue si l'ID du Gestionnaire n'est pas déjà pris
                    if ($this->getRepository(SerieEntity::class)->find($id) instanceof SerieEntity === false) {
                        // on capture les exceptions TypeError de PHP
                        try {
                            // on créer le nouveau Gestionnaire (potentiellement une Exception TypeError)
                            $gestionnaire = (new SerieEntity())
                                ->setId($id)
                                ->setLabel($label)
                                ->setDepot($depot);

                            $starter = (new SerieStarterEntity())
                                ->setSerie($gestionnaire)
                                ->setStarter($starterId)
                                ->setCreatedAt(new \DateTime());

                            $starterErrors = $this->validate($starter);
                            if ($starterErrors->count() >= 1) {
                                foreach ($starterErrors as $error) {
                                    $this->addMessage('danger', $error->getMessage());
                                }
                            } else {
                                $gestionnaire->addStarter($starter);

                                // on persiste l'entity en base de données
                                $errors = $this->persist($gestionnaire);

                                // si aucune erreur lors de la validation on redirige avec un message de succès
                                if ($errors === null) {
                                    if ($request->isXhr()) {
                                        return $response->withJson($gestionnaire, 201);
                                    }

                                    $gestionnaire_name = TwigExtensions::serieLabelFilter($gestionnaire, false);
                                    $this->addMessage('success', "Le Gestionnaire {$gestionnaire_name} a été créé avec succès. Le compteur démarrera à {$gestionnaire->getStarter()}");
                                } else {
                                    $this->addValidatorMessages($errors);
                                }
                            }
                        } catch (\TypeError $e) {
                            $this->addMessage('warning', "Veuillez vérifier les données saisies dans le formulaire");
                        }
                    } else {
                        $this->addMessage('danger', "Le Gestionnaire {$id} existe déjà. Veuillez choisir un autre identifiant");
                    }
                }
            }

            // on redirige toujours vers le formulaire
            return $this->withRedirect($response, 'admin.gestionnaires.create');
        }

        $depots = $this->getRepository(DepotEntity::class)->findAll();
        $series = $this->getRepository(SerieEntity::class)->findAll();
        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'xmethod' => 'POST',
            'context' => 'create',
            'contextMessage' => "Créer",
            'fields' => ['id' => 'required']
        ];

        return $this->render($response, 'admin.gestionnaires.create', compact('depots', 'series', 'form'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $serie_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        /**
         * @var SerieEntity $serie
         */
        $serie = $this->getRepository(SerieEntity::class)->find($serie_id);
        if ($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        if ($request->isPut()) {
            $starter_id = $request->getParsedBodyParam('starter_id', 0);
            $label = $request->getParsedBodyParam('label', null);
            $depot_id = $request->getParsedBodyParam('depot_id', 0);

            try {
                $starter = (new SerieStarterEntity())
                    ->setSerie($serie)
                    ->setStarter($starter_id)
                    ->setCreatedAt(new \DateTime());

                $starterErrors = $this->validate($starter);

                if ($starterErrors->count() === 0) {
                    /**
                     * @var DepotEntity $depot
                     */
                    $depot = $this->getRepository(DepotEntity::class)->find($depot_id);

                    $serie->setDepot($depot);
                    $serie->setLabel($label);

                    if ($serie->getStarter()->getStarter() !== $starter->getStarter()) {
                        $serie->addStarter($starter);
                    }

                    $serieErrors = $this->persist($serie);

                    if ($serieErrors === null) {
                        $gestionnaire_name = TwigExtensions::serieLabelFilter($serie, false);
                        $this->addMessage('success', "Le Gestionnaire {$gestionnaire_name} a été modifié avec succès");
                    } else {
                        $this->addValidatorMessages($serieErrors);
                    }
                } else {
                    foreach ($starterErrors as $error) {
                        $this->addMessage('danger', $error);
                    }
                }
            } catch(\TypeError $e) {
                $this->addMessage('warning', "Veuillez vérifier les données saisies dans le formulaire");
            }

            return $this->withRedirect($response, 'admin.gestionnaires.edit', ['serie_id' => $serie->getId()]);
        }

        $depots = $this->getRepository(DepotEntity::class)->findAll();

        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'xmethod' => 'PUT',
            'context' => 'edit',
            'contextMessage' => "Modifier",
            'data' => $serie,
            'fields' => ['id' => 'disabled']
        ];

        return $this->render($response, 'admin.gestionnaires.edit', [
            'serie' => $serie,
            'depots' => $depots,
            'form'  => $form
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $serie_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function deleteAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        /**
         * @var SerieEntity $serie
         */
        $serie = $this->getRepository(SerieEntity::class)->find($serie_id);
        if ($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        try {
            $this->getDoctrine()->remove($serie);
            $this->getDoctrine()->flush();
        } catch (\Exception $e) {
            switch (get_class($e)) {
                case ForeignKeyConstraintViolationException::class:
                    $error = "Le Gestionnaire ne peut pas être supprimé car il est utilisé ailleurs (commandes et/ou îlots)";
                    break;
                default:
                    $error = "Une erreur est survenur lors de la suppression";
                    break;
            }

            return $response->withJson(['error' => $error], 500);
        }

        return $response->withJson($serie);
    }
}