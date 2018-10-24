<?php
namespace App\Controller;

use App\Entity\Doctrine\CommandeProcessingEntity;
use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\IlotProcessingEntity;
use App\Entity\Doctrine\PlanningChargeEntity;
use App\Entity\Doctrine\PlanningEntity;
use App\Entity\Planning;
use App\Manager\PlanningsManager;
use App\Twig\TwigExtensions;
use Core\Controller\Controller;
use Core\Entity\Entity;
use Doctrine\Common\Util\Debug;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class PlanningsController extends Controller
{
    /**
     * @var string
     */
    protected $menu_item = 'plannings';

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var PlanningEntity[] $plannings
         */
        $plannings = $this->getRepository(PlanningEntity::class)->findAll();

        return $this->render($response, 'planning.indexv2', [
            'plannings' => $plannings
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $planning_slug
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function singleAction(Request $request, Response $response, string $planning_slug): ResponseInterface
    {
        /**
         * @var PlanningEntity|null $planning
         */
        $planning = $this->getRepository(PlanningEntity::class)->findOneBy(['slug' => $planning_slug]);

        // erreur si le planning n'existe pas en base de données
        if ($planning instanceof PlanningEntity === false) {
            throw new NotFoundException($request, $response);
        }

        /**
         * Liste des sources pour le planning FullCalendar.
         *
         * @var array
         * @see https://fullcalendar.io/docs/event_data/events_json_feed/
         */
        $eventsSources = [
            [
                'id' => 'holidays',
                'url' => $this->pathFor('events.holidays')
            ],
            [
                'id' => 'charge',
                'url' => $this->pathFor('plannings.single_events', ['planning_id' => $planning->getId()]),
            ]
        ];

        $planningSeries = [];
        $depots = [];
        $ilotsByDepots = [];

        foreach ($planning->getSeries() as $serie) {
            $planningSeries[] = TwigExtensions::serieLabelFilter($serie, false);
        }

        foreach ($planning->getIlots() as $ilot) {
            $depot_id = $ilot->getLocation()->getId();

            if (array_key_exists($depot_id, $depots) === false) {
                $depots[$depot_id] = $ilot->getLocation();
            }

            $ilotsByDepots[$depot_id][] = $ilot;
        }

        ksort($depots);

        return $this->render($response, 'planning.single', [
            'planning' => $planning,
            'planningSeries' => $planningSeries,
            'eventsSources' => $eventsSources,
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
     * @throws NotFoundException
     */
    public function singleEventsAction(Request $request, Response $response, int $planning_id): ResponseInterface
    {
        /**
         * @var PlanningEntity|null $planning
         */
        $planning = $this->getRepository(PlanningEntity::class)->find($planning_id);

        // erreur si le planning n'existe pas en base de données
        if ($planning instanceof PlanningEntity === false) {
            throw new NotFoundException($request, $response);
        }

        /**
         * @var PlanningsManager $planningsManager
         */
        $planningsManager = $this->getManager(PlanningsManager::class);

        // paramètres start et end envoyés par FullCalendar
        $fcStart = $request->getQueryParam('start', "");
        $fcEnd = $request->getQueryParam('end', "");

        // on créer une instance de \DateTime de start et end ou null s'il n'y a pas les paramètres
        $start = \DateTime::createFromFormat('Y-m-d', $fcStart) ?: null;
        $end = \DateTime::createFromFormat('Y-m-d', $fcEnd) ?: null;

        $processings = $planningsManager->getPlanningProcessings($planning, $start, $end);

        // on a des résultats
        if (!empty($processings)) {
            foreach ($processings as $processing) {
                if ($processing instanceof IlotChargeEntity) {
                    $processing->setEventUrl($this->pathFor('plannings.charge_delete', [
                        'planning_id' => $planning->getId(),
                        'charge_id' => $processing->getId()
                    ]));
                } elseif ($processing instanceof IlotProcessingEntity) {
                    $processing->setEventUrl($this->pathFor('plannings.charge.details', [
                        'charge_id' => $processing->getId()
                    ]));
                }
            }

            Entity::toEvent();
            return $response->withJson($processings);
        }

        return $response->withStatus(204);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $charge_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function chargeDetailsAction(Request $request, Response $response, string $charge_id): ResponseInterface
    {
        /**
         * @var IlotProcessingEntity $charge
         */
        $fromCharge = $this->getRepository(IlotProcessingEntity::class)->find($charge_id);

        if ($fromCharge instanceof IlotProcessingEntity === false) {
            throw new NotFoundException($request, $response);
        }

        if ($fromCharge['charge'] instanceof IlotChargeEntity) {
            $jsonCharge = new Planning\IlotChargeEvent($fromCharge['charge']);
            $jsonCharge->setUrl($this->pathFor('plannings.charge_update', [
                'charge_id' => $jsonCharge->getId()
            ])); // url pour le javascript
        }

        $charges = $this->getRepository(CommandeProcessingEntity::class)->findBy([
            'ilot' => $fromCharge->getIlot(),
            'processing_at' => $fromCharge->getDate()
        ]);

        return $this->render($response, 'planning.charge_details.inc', compact('fromCharge', 'charges', 'jsonCharge'));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function addChargeAction(Request $request, Response $response): ResponseInterface
    {
        $quantity = (int) $request->getParsedBodyParam('quantity', 0);

        // Date d'ajout de l'évènement
        $quantity_at = $request->getParsedBodyParam('quantity_at', new \DateTime());
        if($quantity_at instanceof \DateTime === false) {
            $quantity_at = \DateTime::createFromFormat("d/m/Y", $quantity_at);
        }

        $ilot_id = $request->getParsedBodyParam('ilot_id', 0);
        /**
         * @var IlotEntity $ilot
         */
        $ilot = $this->getRepository(IlotEntity::class)->find($ilot_id);

        if ($ilot instanceof IlotEntity === false) {
            return $response->withJson(['errors' => ['ilot_id' => "Cet îlot n'existe pas"]], 400);
        }

        // un evènement a déjà été saisi pour ce jour, on affiche une erreur
        $eventAlreadyExists = $this->getRepository(IlotChargeEntity::class)->findOneBy(['ilot' => $ilot->getId(), 'quantity_at' => $quantity_at]);
        if($eventAlreadyExists instanceof IlotChargeEntity) {
            return $response->withJson(
                ['errors' => [
                    'quantity_at' => [
                        'input' => $quantity_at->format(DATE_ISO8601),
                        'message' => "Une disponibilité de {$eventAlreadyExists->getQuantity()} pièces existe déjà pour le " . $quantity_at->format('d/m/Y')]
                ]], 400
            );
        }

        // Création de l'évènement
        $event = (new IlotChargeEntity())
            ->setIlot($ilot)
            ->setQuantity($quantity)
            ->setQuantityAt($quantity_at);

        // Sauvegarde de l'évènement en base de données
        $errors = $this->persist($event);
        if($errors !== null) {
            return $response->withJson(['errors' => $errors], 400);
        }

        $event->setEventUrl($this->pathFor('plannings.charge_update', ['charge_id' => $event->getId()]));
        $eventJSON = (new Planning\IlotChargeEvent($event));

        // On renvoi l'évènement en JSON
        return $response->withJson($eventJSON);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $event_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function editChargeAction(Request $request, Response $response, int $event_id): ResponseInterface
    {
        // on récupère l'évènement en base de données
        /**
         * @var IlotChargeEntity $event
         */
        $event = $this->getRepository(IlotChargeEntity::class)->find($event_id);
        if($event instanceof IlotChargeEntity === false) {
            throw new NotFoundException($request, $response);
        }

        // on clone l'event issu de la bdd pour le renvoyer comme il était avant update
        $originalEvent = clone $event;

        $quantity = $request->getParsedBodyParam('quantity', false);
        $quantity_at = $request->getParsedBodyParam('quantity_at', false);

        if($quantity !== false) {
            $event->setQuantity((int) $quantity);
        }
        if($quantity_at !== false) {
            $quantity_at = \DateTime::createFromFormat('d/m/Y', $quantity_at);
            $event->setQuantityAt($quantity_at);
        }

        // un evènement a déjà été saisi pour ce jour, on affiche une erreur
        $eventAlreadyExists = $this->getRepository(IlotChargeEntity::class)->findOneBy([
            'ilot' => $originalEvent->getIlot()->getId(),
            'quantity_at' => $quantity_at
        ]);

        if(
            $eventAlreadyExists instanceof IlotChargeEntity
            && $eventAlreadyExists->getId() !== $originalEvent->getId()
        ) {
            return $response->withJson(
                ['errors' => [
                    'quantity_at' => [
                        'input' => $quantity_at->format(DATE_ISO8601),
                        'message' => "{$eventAlreadyExists->getIlot()->getName()}: une disponibilité de {$eventAlreadyExists->getQuantity()} pièces a déjà été saisie pour le " . $quantity_at->format('d/m/Y')]
                ]], 400
            );
        }

        if($event->getQuantity() !== $originalEvent->getQuantity()
            || $event->getQuantityAt()->format('d/m/Y') !== $originalEvent->getQuantityAt()->format('d/m/Y')
        ) {
            if(($errors = $this->persist($event)) !== null) {
                return $response->withJson(['errors' => $errors], 400);
            }

            $eventJSON = (new Planning\IlotChargeEvent($event))
                ->setData('previous', new Planning\IlotChargeEvent($originalEvent));

            return $response->withJson($eventJSON);
        }

        return $response->withStatus(204);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $charge_id
     *
     * @return ResponseInterface
     */
    public function removeChargeAction(Request $request, Response $response, int $charge_id): ResponseInterface
    {
        $charge = $this->getRepository(IlotChargeEntity::class)->find($charge_id);

        if ($charge instanceof IlotChargeEntity) {
            $this->getDoctrine()->remove($charge);
            $this->getDoctrine()->flush();

            return $response->withStatus(204);
        }

        return $response->withStatus(400);
    }
}