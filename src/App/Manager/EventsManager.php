<?php
namespace App\Manager;

use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\IlotRemainingChargeEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Planning\IlotChargeEvent;
use App\Entity\Planning\IlotGestionnairesChargeEvent;
use App\Entity\Planning\IlotGestionnairesRemainingChargeEvent;
use App\Entity\Planning\IlotRemainingChargeEvent;
use Core\Manager\Manager;

class EventsManager extends Manager
{
    /**
     * @param IlotEntity $ilot
     * @param string     $start
     * @param string     $end
     *
     * @return IlotChargeEvent[]
     */
    public function getIlotEventsBetween(IlotEntity $ilot, string $start, string $end): array
    {
        $qb = $this->getRepository(IlotChargeEntity::class)->createQueryBuilder('charge')
            ->where('charge.ilot = :ilot_id')
            ->andWhere('charge.quantity_at BETWEEN :start AND :end');

        $qb->setParameter('ilot_id', $ilot->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        $events = $qb->getQuery()->getResult();
        if (!empty($events)) {
            foreach ($events as $k => $event) {
                $events[$k] = (new IlotChargeEvent($event));
            }

            return $events;
        }

        return [];
    }

    /**
     * @param SerieEntity $serie
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return array
     */
    public function getGestionnairesIlots(SerieEntity $serie, \DateTime $start, \DateTime $end): array
    {
        $events = $this->getManager(IlotsManager::class)->getIlotsProcessingCharge($serie, $start, $end);

        if (!empty($events)) {
            foreach ($events as $k => $event) {
                if ($event instanceof IlotRemainingChargeEntity) {
                    $events[$k] = (new IlotGestionnairesRemainingChargeEvent($event));
                } elseif ($event instanceof IlotChargeEntity) {
                    $events[$k] = (new IlotGestionnairesChargeEvent($event));
                }
            }

            return $events;
        }

        return [];
    }

    /**
     * @param IlotEntity $ilot
     * @param string $start
     * @param string $end
     *
     * @return IlotRemainingChargeEvent[]
     */
    public function getCommandesEvents(IlotEntity $ilot, string $start, string $end): array
    {
        $qb = $this->getRepository(IlotRemainingChargeEntity::class)->createQueryBuilder('charge')
            ->where('charge.ilot = :ilot_id')
            ->andWhere('charge.quantity_at BETWEEN :start AND :end');

        $qb->setParameter('ilot_id', $ilot->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        /**
         * @var IlotRemainingChargeEntity[] $events
         */
        $events = $qb->getQuery()->getResult();

        if (!empty($events)) {
            foreach ($events as $k => $event) {
                // on supprimer les lignes qui n'ont pas de mouvements (à défaut de le faire dans la requête SQL)
                // “Illegal mix of collations” error in mysql
                if ($event->getFromTable() === 'idc') {
                  unset($events[$k]);
                } else {
                    $events[$k] = (new IlotRemainingChargeEvent($event));
                }
            }

            return $events;
        }

        return [];
    }
}