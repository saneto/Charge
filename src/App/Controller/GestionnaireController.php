<?php
namespace App\Controller;

use App\Entity\Doctrine\CommentTypeEntity;
use App\Entity\Doctrine\DepotEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Doctrine\CommandeEntity;
use App\Entity\Doctrine\CasValueEntity;
use App\Entity\Doctrine\CommandeProcessingEntity;
use App\Entity\Doctrine\CommentEntity;
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
use Doctrine\ORM\Query\Expr\Join;

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
        $series = $this->getManager(SeriesManager::class)->findBy([], ['id' => 'ASC']);
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
        $vendor = $this->container->auth->getIdentity();
        $starter =  $this->getManager(SeriesStartersManager::class)->getNextSerieStarterbyUser($serie, $vendor->getId());





        if($serie instanceof SerieEntity === false) {
            throw new NotFoundException($request, $response);
        }

        $ilotsManager = $this->getManager(IlotsManager::class);
        $depotsManager = $this->getManager(DepotsManager::class);

        /**
         * @var IlotEntity[] $ilotsSerie
         */

        $ilotsSerie = [];
        $depots = $depotsManager->findBy(['open' => '1']);

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
        $casVme = $this->getRepository(CasValueEntity::class)->findAll();
       // $starter = $this->getManager(SeriesStartersManager::class)->getNextSerieStarter($serie);
        $date   = new \DateTime();
        $start =  $date->format('ym');
        return $this->render($response, 'gestionnaires.reserve', compact('starter', 'serie','start','casVme', 'depots', 'ilots', 'datepicker_days', 'comments_types'));
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

        $start = $request->getQueryParam('start', new \DateTime());
        if ($start instanceof \DateTime === false) {
            $start = \DateTime::createFromFormat('Y-m-d', $start);
        }

        $end = $request->getQueryParam('end', $start);
        if ($end instanceof \DateTime === false) {
            $end = \DateTime::createFromFormat('Y-m-d', $end);
        }
        /**
         * @var PlanningsManager $planningsManager
         */
        $planningsManager = $this->getManager(PlanningsManager::class);
        $charges = $planningsManager->getPlanningProcessings( $serie_id, $start, $end);

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
    }
}
