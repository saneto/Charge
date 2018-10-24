<?php
namespace App\Controller;

use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Planning\DepotEventEntity;
use App\Entity\Planning\HolidayEventEntity;
use Core\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Yasumi\Holiday;
use Yasumi\Yasumi;

class EventsController extends Controller
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function holidaysAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * Représentation JSON des jours fériés pour le planning.
         * @var array $json
         */
        $json = [];

        /**
         * Date de départ à laquelle inclure les jours fériés.
         * @var null|string|\DateTime $start
         */
        $start = strip_tags($request->getQueryParam('start'));

        /**
         * Date de fin à laquelle inclure les jours fériés.
         * @var null|string|\DateTime $end
         */
        $end   = strip_tags($request->getQueryParam('end'));

        // si on a les paramètres start et end dans l'URI on les transforme en DateTime PHP
        if ($start !== "" && $end !== "") {
            // interval de 1 jour
            $oneDay = new \DateInterval('P01D');

            $oneDayBefore = clone $oneDay;
            $oneDayBefore->invert = 1; // interval inversé (donc moins 1 jour)

            $oneDayAfter = clone $oneDay;

            $start = (\DateTime::createFromFormat('Y-m-d', $start))
                ->add($oneDayBefore);
            $end = (\DateTime::createFromFormat('Y-m-d', $end))
                ->add($oneDayAfter);
        }

        // les paramètres start et end étaient au format attendu, alors on travail sur l'interval
        if ($start instanceof \DateTime && $end instanceof \DateTime)
        {
            $startYear = $start->format('Y');
            $endYear   = $end->format('Y');

            // on ne chevauche pas une année sur l'autre
            if ($startYear === $endYear) {
                $holidays = (Yasumi::create('France', $startYear, 'fr_FR'))
                    ->between($start, $end, false);
            } elseif ($endYear > $startYear) {
                $holidays = [];

                // on demande les jours fériés de l'année de début
                $holidaysStart = (Yasumi::create('France', $startYear, 'fr_FR'))
                    ->between($start, $end, false);
                // on demande les jours fériés de l'année de fin
                $holidaysEnd = (Yasumi::create('France', $endYear, 'fr_FR'))
                    ->between($start, $end, false);

                // on récupère chaque jours fériés de l'année de début
                foreach ($holidaysStart as $holidayStart) {
                    $holidays[] = $holidayStart;
                }
                // on récupère chaque jours fériés de l'année de fin
                foreach ($holidaysEnd as $holidayEnd) {
                    $holidays[] = $holidayEnd;
                }
            } else {
                throw new \InvalidArgumentException("Invalid date interval");
            }
        } else {
            $year = date('Y');
            $holidays = Yasumi::create('France', $year, 'fr_FR');
        }

        /**
         * Contient les jours fériés.
         * @var Holiday[] $holidays
         */
        foreach ($holidays as $k => $holiday) {
            $json[] = (new HolidayEventEntity($holiday));
        }

        /*$start = new \DateTime();
        $end = clone $start;
        $end->add(new \DateInterval('P05D'));

        $json[] = (new DepotEventEntity())
            ->setTitle("Fermeture de Noël")
            ->setStart($start)
            ->setEnd($end);*/

        return (!empty($json)) ? $response->withJson($json) : $response->withStatus(204);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $event_id
     *
     * @return ResponseInterface
     */
    public function resumeAction(Request $request, Response $response, int $event_id): ResponseInterface
    {
        $event = $this->_getIlotCharge($event_id);

        return $response->withJson($event);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $event_id
     *
     * @return ResponseInterface
     *
     * @throws NotFoundException
     */
    public function updateAction(Request $request, Response $response, int $event_id): ResponseInterface
    {
        $event = $this->_getIlotCharge($event_id);

        $quantity = $request->getParsedBodyParam('quantity', 0);
        // $quantity_at = $request->getParsedBodyParam('start');

        $event->setQuantity($quantity);

        $errors = $this->persist($event);
        if($errors !== null) {
            return $response->withJson(['errors' => $errors], 400);
        }

        return $response;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $event_id
     *
     * @return ResponseInterface
     */
    public function deleteAction(Request $request, Response $response, int $event_id): ResponseInterface
    {
        $event = $this->_getIlotCharge($event_id);

        $this->getDoctrine()->remove($event);
        $this->getDoctrine()->flush();

        return $response->withStatus(204);
    }

    /**
     * @param int           $event_id
     * @param null|Request  $request
     * @param null|Response $response
     *
     * @return IlotChargeEntity
     *
     * @throws NotFoundException
     */
    private function _getIlotCharge(
        int $event_id,
        ?Request $request = null,
        ?Response $response = null
    ): IlotChargeEntity
    {
        $event = $this->getRepository(IlotChargeEntity::class)->find($event_id);
        if(!$event instanceof IlotChargeEntity) {
            throw new NotFoundException($request ?? $this->container['request'], $response ?? $this->container['response']);
        }

        return $event;
    }
}
