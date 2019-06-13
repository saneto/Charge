<?php
namespace App\Manager;

use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\IlotRemainingChargeEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Twig\TwigExtensions;
use Core\Manager\Manager;
use Doctrine\ORM\Query\Expr\Join;

class IlotsManager extends Manager
{
    /**
     * @var string
     */
    protected $entityName = IlotEntity::class;

    /**
     * @internal
     *
     * @param IlotEntity $ilot
     *
     * @return array
     */
    public function getNearest(IlotEntity $ilot)
    {
        $qb = $this->getRepository()->createQueryBuilder('i')
            ->where("i.location = :depot_id")
            ->andWhere("i.id != :ilot_id");

        $qb->setParameter('depot_id', $ilot->getLocation()->getId())
            ->setParameter('ilot_id', $ilot->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int            $serie_id
     * @param null|\DateTime $quantity_at
     *
     * @return array
     */
    public function getIlotsWithCharge(int $serie_id, ?\DateTime $quantity_at = null)
    {
        if($quantity_at === null) {
            $quantity_at = new \DateTime();
        }

        $qb = $this->getRepository()->createQueryBuilder('i')
            ->select('i AS ilot', 'charge.quantity')
            ->where('charge.quantity_at BETWEEN :start AND :end')
            ->leftJoin(
                IlotChargeEntity::class, 'charge',
                Join::WITH, 'charge.ilot = i.id'
            )
            ->orderBy('charge.quantity', 'ASC');

        $qb->setParameter('start', $quantity_at->format('Y-m-d'))
            ->setParameter('end', $quantity_at->format('Y-m-d'));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $serie_id
     *
     * @return array
     */
    public function getIlotsChargeDays(int $serie_id): array
    {
        $qb = $this->getRepository()->createQueryBuilder('i')
            ->select('charge.quantity, charge.quantity_at')
            ->where('charge.quantity_at BETWEEN :min AND :max')
            ->leftJoin(
                IlotChargeEntity::class, 'charge',
                Join::WITH, 'charge.ilot = i.id'
            )
            ->orderBy('charge.quantity_at', 'ASC');

        $qb->setParameter('min', new \DateTime('-2 months'))
            ->setParameter('max', new \DateTime('+2 months'));

        $result = $qb->getQuery()->getArrayResult();

        if(!empty($result)) {
            $formatted = [];

            foreach($result as $charge) {
                $k = $charge['quantity_at']->format('Y-m-d');
                $formatted[$k] = TwigExtensions::colorizeQuantityFilter($charge['quantity']);
            }

            return $formatted;
        }

        return [];
    }

    /**
     * @param SerieEntity $serie
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return IlotRemainingChargeEntity[]
     */
    public function getIlotsProcessingCharge(SerieEntity $serie, \DateTime $start, \DateTime $end): array
    {
        $irc = $this->getDoctrine()->createQueryBuilder();
        // $idc = $this->getDoctrine()->createQueryBuilder();

        $events1 = $irc->select('irc')
            ->from(IlotRemainingChargeEntity::class, 'irc')
            ->leftJoin(
                IlotEntity::class, 'i',
                Join::WITH, 'i.id = irc.ilot'
            )
            ->where('i.serie = :serie_id')
            ->andWhere('irc.quantity_at BETWEEN :start AND :end')
            ->setParameters([
                'serie_id' => $serie->getId(),
                'start'    => $start->format('Y-m-d'),
                'end'      => $end->format('Y-m-d')
            ])
            ->getQuery()->getResult();

        /*$events2 = $idc->select('idc')
            ->from(IlotChargeEntity::class, 'idc')
            ->leftJoin(
                IlotEntity::class, 'iidc',
                Join::WITH, 'iidc.id = idc.ilot'
            )
            ->where('iidc.serie = :serie_id')
            ->andWhere($idc->expr()->notIn('idc.id', $irc->getDQL()))
            ->andWhere('idc.quantity_at BETWEEN :start AND :end')
            ->setParameters([
                'serie_id' => $serie->getId(),
                'start'    => $start->format('Y-m-d'),
                'end'      => $end->format('Y-m-d')
            ])
            ->getQuery()->getResult();*/

        // return array_merge($events1, $events2);
        return $events1;
    }
}
