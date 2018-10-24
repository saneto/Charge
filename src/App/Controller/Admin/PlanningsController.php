<?php
namespace App\Controller\Admin;

use App\Entity\Doctrine;
use App\Manager;
use App\Provider\AppProvider;
use App\Utils;
use Core\Controller\Controller;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class PlanningsController extends Controller
{
    /**
     * @var string
     */
    protected $menu_item = "plannings";

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var Manager\PlanningsManager $planningsManager
         */
        $planningsManager = $this->getManager(Manager\PlanningsManager::class);

        return $this->render($response, 'admin.plannings.index', [
            'plannings' => $planningsManager->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response): ResponseInterface
    {
        // on envoi le formulaire pour créer un planning
        if ($request->isPost()) {
            $created = $this->_createOrEdit($request, $response, $newPlanning);

            if ($created->getStatusCode() === 201) {
                $this->addMessage('success', "Le planning {$newPlanning} a été créé avec succès");
                return $this->withRedirect($response, 'admin.plannings.edit', [
                    'planning_slug' => $newPlanning->getSlug()
                ]);
            }

            return $this->withRedirect($response, 'admin.plannings.create');
        }

        /**
         * @var Manager\SeriesManager $seriesManager
         */
        $seriesManager = $this->getManager(Manager\SeriesManager::class);

        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'context' => 'create',
            'contextMessage' => "Créer"
        ];

        return $this->render($response, 'admin.plannings.create', [
            'form' => $form,
            'series' => $seriesManager->findAll(),
            'plannings' => $this->getRepository(Doctrine\PlanningEntity::class)->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $planning_slug
     *
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(Request $request, Response $response, string $planning_slug): ResponseInterface
    {
        /**
         * @var Doctrine\PlanningEntity $planning
         */
        $planning = $this->getRepository(Doctrine\PlanningEntity::class)->findOneBy(['slug' => $planning_slug]);

        if ($planning instanceof Doctrine\PlanningEntity === false) {
            return new NotFoundException($request, $response);
        }

        // on envoi le formulaire pour modifier un planning
        if ($request->isPut()) {
            $slug = $planning->getSlug();
            $edited = $this->_createOrEdit($request, $response, $planning, $newPlanning);

            if ($edited->getStatusCode() === 200) {
                $slug = $newPlanning->getSlug();

                if ($planning->getSlug() !== $newPlanning->getSlug()) {
                    $this->addMessage('success', "Le planning {$planning} a bien été renommé en {$newPlanning}");
                } else {
                    $this->addMessage('success', "Le planning {$newPlanning} a été modifié avec succès");
                }
            }

            return $this->withRedirect($response, 'admin.plannings.edit', ['planning_slug' => $slug]);
        }

        $planningSeries = $planning->getSeries()->toArray();
        $planningSeriesIds = [];

        foreach ($planningSeries as $serie) {
            $planningSeriesIds[] = $serie->getId();
        }

        /**
         * @var EntityRepository $seriesRepo
         */
        $seriesRepo = $this->getRepository(Doctrine\SerieEntity::class);

        if (!empty($planningSeriesIds)) {
            $series = $seriesRepo->createQueryBuilder('sri')
                ->where('sri.id NOT IN (:planning_series)')
                ->setParameter(':planning_series', $planningSeriesIds)
                ->getQuery()->getResult();

            foreach ($series as $serie) {
                array_push($planningSeries, $serie);
            }
        } else {
            $planningSeries = $seriesRepo->findAll();
        }

        $depots = [];
        $ilotsByDepots = [];

        foreach ($planning->getIlots() as $ilot) {
            $depot_id = $ilot->getLocation()->getId();

            if (array_key_exists($depot_id, $depots) === false) {
                $depots[$depot_id] = $ilot->getLocation();
            }

            $ilotsByDepots[$depot_id][] = $ilot;
        }

        $form = [
            'action' => (string) $request->getUri(),
            'method' => 'POST',
            'xmethod' => 'PUT',
            'context' => 'edit',
            'contextMessage' => "Modifier",
            'data' => $planning
        ];

        return $this->render($response, 'admin.plannings.edit', [
            'form' => $form,
            'planning' => $planning,
            'series' => $planningSeries,
            'planningSeries' => $planningSeriesIds,
            'depots' => $depots,
            'ilotsByDepots' => $ilotsByDepots
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $planning_id
     *
     * @return ResponseInterface
     */
    public function deleteAction(Request $request, Response $response, int $planning_id): ResponseInterface
    {
        /**
         * @var Doctrine\PlanningEntity $planning
         */
        $planning = $this->getRepository(Doctrine\PlanningEntity::class)->find($planning_id);

        if ($planning instanceof Doctrine\PlanningEntity === false) {
            return new NotFoundException($request, $response);
        }

        try {
            $this->getDoctrine()->remove($planning);
            $this->getDoctrine()->flush();

            $message = "Le planning {$planning} a bien été supprimé";

            return $response->withJson([
                'message' => $message,
                'location' => $this->pathFor('admin.plannings.index')
            ], 200);
        } catch (\Exception $e) {
            $message = "Le planning {$planning} ne peut pas être supprimé";

            if (AppProvider::getEnv() === "dev") {
                $message .= $e->getMessage();
            }

            return $response->withJson(['error' => $message], 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Doctrine\PlanningEntity|null $existingPlanning
     * @param Doctrine\PlanningEntity $newPlanning
     *
     * @return ResponseInterface|Doctrine\PlanningEntity
     * @throws MethodNotAllowedException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function _createOrEdit(
        Request $request,
        Response $response,
        ?Doctrine\PlanningEntity &$existingPlanning = null,
        &$newPlanning = null
    )
    {
        $isCreating = $request->isPost(); // on créer un nouveau planning
        $isEditing = $request->isPut(); // on modifie un planning existant
        $isSuccess = false; // on attent que le planning soit validé

        /**
         * @var EntityRepository $seriesRepo
         */
        $seriesRepo = $this->getRepository(Doctrine\SerieEntity::class);

        if ($isCreating) {
            $planning = (new Doctrine\PlanningEntity());
        } elseif ($isEditing) {
            $planning = clone $existingPlanning;

            if ($planning instanceof Doctrine\PlanningEntity === false) {
                throw new \LogicException("Vous devez préciser l'argument \$planning en mode édition");
            }
        } else {
            throw new MethodNotAllowedException($request, $response, ['POST', 'PUT']);
        }

        // on récupère les informations du formulaire
        $planning_label = $request->getParsedBodyParam('label', null);
        $planning_color = (new Utils\HexaColor)
            ->setColor($request->getParsedBodyParam('color', "#000000"));
        $planning_series = $request->getParsedBodyParam('series', []);

        /**
         * On ne recherche que les séries demandées dans le formulaire.
         * @var Doctrine\SerieEntity[] $series
         */
        $series = $seriesRepo->findBy(['id' => $planning_series]);

        try {
            // on manipule notre planning
            $planning->setLabel($planning_label)
                ->setColor($planning_color->getColor())
                ->attachSeries($series);

            if ($isEditing) {
                $planning = $this->getDoctrine()->merge($planning);
            }

            // on sauvegarde le planning en base de données
            $errors = $this->persist($planning);

            if ($errors === null) {
                // tout s'est bien passé !
                $isSuccess = true;
            } else {
                $this->addValidatorMessages($errors);
            }
        } catch (\TypeError $e) {
            $message = "Veuillez vérifier les informations saisie dans le formulaire";

            if (AppProvider::getEnv() === "dev") {
                $message .= " " . $e->getMessage();
            }

            $this->addMessage('warning', $message);
        } catch (UniqueConstraintViolationException $e) {
            $message = "Un autre planning porte déjà le nom \"{$planning->getLabel()}\"";

            if (AppProvider::getEnv() === "dev") {
                $message .= " " . $e->getMessage();
            }

            $this->addMessage('warning', $message);
        }

        // tout s'est bien passé !
        if ($isSuccess) {
            if ($isCreating) {
                $existingPlanning = $planning;
            } elseif ($isEditing) {
                $newPlanning = $planning;
            }

            return $response->withStatus($isCreating ? 201 : 200);
        }

        // on a eu une erreur ...
        return $response->withStatus(500);
    }
}