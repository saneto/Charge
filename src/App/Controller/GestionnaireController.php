<?php
namespace App\Controller;

use App\Entity\Doctrine\CommentTypeEntity;
use App\Entity\Doctrine\DepotEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Planning\IlotGestionnairesChargeEvent;
use App\Exception\NoSerieAttachedToPlanning;
use App\Exception\NotAttachedToPlanning;
use App\Manager\DepotsManager;
use App\Manager\IlotsManager;
use App\Manager\PlanningsManager;
use App\Manager\SeriesManager;
use App\Manager\SeriesStartersManager;
use Core\Controller\Controller;
use Core\Entity\Entity;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class GestionnaireController extends Controller
{
    protected $menu_item = 'gestionnaires';

    /**
     * Affiche la liste des séries pour une réservation.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var $seriesManager SeriesManager
         */
        $seriesManager = $this->getManager(SeriesManager::class);
        $series = $seriesManager->findBy([], ['id' => 'ASC']);
        echo($series);
        return $this->render($response, 'gestionnaires.index', compact('series'));
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $serie_id
     *
     * @return ResponseInterface
     *
     * @throws NotFoundException
     */
    public function reserveAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        /**
         * @var $ilotsManager IlotsManager
         * @var $depotsManager DepotsManager
         * @var $serie SerieEntity
         * @var $depots DepotEntity[]
         */

        $serie = $this->getRepository(SerieEntity::class)->find($serie_id);
        if($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        $ilotsManager = $this->getManager(IlotsManager::class);
        $depotsManager = $this->getManager(DepotsManager::class);

        /**
         * @var IlotEntity[] $ilotsSerie
         */
        $ilotsSerie = [];
        $depots = $depotsManager->findAll();

        $planningsSerie = $serie->getPlannings();

        if ($planningsSerie->isEmpty()) {
            throw new NotAttachedToPlanning($request, $response, $this->container->view, $serie);
        }

        foreach ($planningsSerie as $planningSerie) {
            if (count($planningSerie->getIlots()) >= 1) {
                $ilotsSerie = array_merge($ilotsSerie, $planningSerie->getIlots()->toArray());
            }
        }

        $ilots = [];
        foreach ($ilotsSerie as $ilotSerie) {
            $ilots[$ilotSerie->getLocation()->getId()][] = $ilotSerie;
        }

        if (empty($ilots)) {
            throw new NoSerieAttachedToPlanning($request, $response, $this->container->view, $planningsSerie->toArray(), $serie);
        }

        ksort($ilots);

        $comments_types = $this->getRepository(CommentTypeEntity::class)->findAll();
        $datepicker_days = $ilotsManager->getIlotsChargeDays($serie_id);

        $starter = $this->getManager(SeriesStartersManager::class)->getNextSerieStarter($serie);

        return $this->render($response, 'gestionnaires.reserve', compact('starter', 'serie', 'depots', 'ilots', 'datepicker_days', 'comments_types'));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $serie_id
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function ilotsEvents_ajaxAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        /**
         * @var SerieEntity $serie
         */
        $serie = $this->getRepository(SerieEntity::class)->findOneBy(['id' => $serie_id]);
        if($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        $start = $request->getQueryParam('start', new \DateTime());
        if ($start instanceof \DateTime === false) {
            $start = \DateTime::createFromFormat('Y-m-d', $start);
        }

        $end = $request->getQueryParam('end', $start);
        if ($end instanceof \DateTime === false) {
            $end = \DateTime::createFromFormat('Y-m-d', $end);
        }

        $charges = [];
        /**
         * @var PlanningsManager $planningsManager
         */
        $planningsManager = $this->getManager(PlanningsManager::class);

        foreach ($serie->getPlannings() as $planning) {
            $processings = $planningsManager->getPlanningProcessings($planning, $start, $end);

            for ($i = 0; $i < count($processings); $i++) {
                array_push($charges, $processings[$i]);
            }
        }

        if (!empty($charges)) {
            Entity::toEvent();

            $charges = array_map(function ($entity) {
                return (new IlotGestionnairesChargeEvent($entity));
            }, $charges);

            return $response->withJson($charges);
        }

        return $response->withStatus(204);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $serie_id
     *
     * @return ResponseInterface
     *
     * @throws NotFoundException
     */
    public function ilotsQuantities_ajaxAction(Request $request, Response $response, int $serie_id): ResponseInterface
    {
        // on valide que la série demandée existe
        $serie = $this->getRepository(SerieEntity::class)->findOneBy(['id' => $serie_id]);
        if($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        // on formate la date envoyée: par défaut si pas de date précisée, on choisi la date courante
        $quantity_at = $request->getQueryParam('quantity_at');
        if ($quantity_at === "Aujourd'hui") {
            $quantity_at = new \DateTime();
        }  elseif($quantity_at !== null) {
            $quantity_at = \DateTime::createFromFormat("d/m/Y", $quantity_at);
        }

        if ($quantity_at === false) {
            return $response->withStatus(400);
        }

        $json = [];

        /**
         * @var $ilots IlotEntity[]
         * @var $ilot IlotEntity
         */
        $ilots = $this->getManager(IlotsManager::class)->getIlotsWithCharge($serie_id, $quantity_at);

        if (empty($ilots)) {
            $ilots = $this->getManager(IlotsManager::class)->findAll();
        }

        foreach ($ilots as $item) {
            $ilot = $item['ilot'] ?? $item;

            $json[$ilot->getLocation()->getId()]['depot'] = $ilot->getLocation();
            $json[$ilot->getLocation()->getId()]['ilots'][] = $item;
        }

        return $response->withJson($json);

        return $response->withStatus(204);
        // return $this->render($response, 'gestionnaires.ilots_quantities.inc', ['ilots' => $ilots]);
    }
}
