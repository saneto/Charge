<?php
namespace App\Manager;

use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Doctrine\IlotEntity;
use Core\Manager\Manager;

class IlotsDailyChargeManager extends Manager
{
    /**
     * @var string
     */
    protected $entityName = IlotChargeEntity::class;

    /**
     * @param IlotEntity $ilot
     * @param string     $start
     * @param string     $end
     *
     * @return array
     */
    public function getIlotEventsBetween(IlotEntity $ilot, string $start, string $end)
    {
        $qb = $this->getRepository()->createQueryBuilder('i');

        $qb->where('i.ilot = :ilot_id')
            ->andWhere('i.quantity_at BETWEEN :start AND :end');

        $qb->setParameter('ilot_id', $ilot->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        return $qb->getQuery()->getResult();
    }
}
