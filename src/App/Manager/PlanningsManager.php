<?php
namespace App\Manager;

use App\Entity\Doctrine;
use Core\Manager\Manager;
use Doctrine\ORM\Query\Expr\Join;


class PlanningsManager extends Manager
{
    protected $entityName = Doctrine\PlanningEntity::class;

    /**
     * @param Doctrine\PlanningEntity $planning
     * @param \DateTime|null $minDate
     * @param \DateTime|null $maxDate
     *
     * @return \App\Entity\Doctrine\IlotProcessingEntity[]
     */


    public function getPlanningProcessings(int $serie_id, ?\DateTime $minDate = null, ?\DateTime $maxDate = null)
    {
        if ($minDate instanceof \DateTime && $maxDate instanceof \DateTime) {

            $oneDayBefore = new \DateInterval('P01D');
            $oneDayBefore->invert = 1;

            $minDate->add($oneDayBefore);

            $processings = $this->getIlotProcesseValue($serie_id,  $minDate, $maxDate);

            // on sélectionne toutes les charges de départ des îlots du planning
            $processingsCharges = [];
            foreach ($processings as $processing) {
                if ($processing->getCharge() instanceof Doctrine\IlotChargeEntity) {
                    $processingsCharges[] = $processing->getCharge()->getId();
                }
            }

            $charges = $this->getChargeProcesseValue($serie_id, $processingsCharges,  $minDate, $maxDate);

            foreach ($charges as $charge) {
                array_push($processings, $charge);
            }
           return $processings;
        } else {
            return null;
        }
    }

    private function getIlotProcesseValue(int $serie_id, ?\DateTime $minDate = null, ?\DateTime $maxDate = null)
    {
            $qdl =  ' SELECT ipe FROM  App\Entity\Doctrine\IlotProcessingEntity ipe  JOIN ipe.planning ipse JOIN ipse.series se WITH se.id = :serie_id '.
                    ' WHERE  ipe.date BETWEEN :minDate AND :maxDate ';
            return $this->getDoctrine()->createQuery($qdl)
                            ->setParameter(':serie_id', $serie_id)
                            ->setParameter(':minDate', $minDate)
                            ->setParameter(':maxDate', $maxDate)
                            ->getResult();
    }


    private function getChargeProcesseValue(int $serie_id,$processingsCharges , ?\DateTime $minDate = null, ?\DateTime $maxDate = null)
    {
        $qbCharges = $this->getRepository(Doctrine\PlanningIlotEntity::class)->createQueryBuilder('plni');
        $charges = $qbCharges->select('iltc')
            ->leftJoin(
                Doctrine\IlotChargeEntity::class, 'iltc',
                Join::WITH, 'iltc.ilot = plni.ilot'
            )
            ->leftJoin( 'plni.planning',  'ipse')
            ->leftJoin(
                'ipse.series',  'se'
            )
            ->where('iltc.quantity_at BETWEEN :minDate AND :maxDate')
            ->andWhere(' se.id = :serie_id  ');


        if (!empty($processingsCharges)) {
            // mais on n'inclus pas les charges de départ si on a des prélèvements
            $charges = $charges->andWhere($qbCharges->expr()->notIn('iltc.id', $processingsCharges));
        }

        return $charges->setParameter(':serie_id', $serie_id)
            ->setParameter(':minDate', $minDate)
            ->setParameter(':maxDate', $maxDate)
            ->getQuery()->getResult();
    }


    public function getPlanningProcessingsWithPlan(Doctrine\PlanningEntity $planning, ?\DateTime $minDate = null, ?\DateTime $maxDate = null)
    {
        if ($minDate instanceof \DateTime && $maxDate instanceof \DateTime) {
            // on inclue le jour même (le jour même moins un jour)
            $oneDayBefore = new \DateInterval('P01D');
            $oneDayBefore->invert = 1;
            $minDate->add($oneDayBefore);
            /**
             * On récupère le totla des prélèvements pour les îlots attachés au planning.
             * @var Doctrine\IlotProcessingEntity[] $processings
             */
            $processings = $this->getRepository(Doctrine\IlotProcessingEntity::class)->createQueryBuilder('iltp')
                ->where('iltp.planning = :planning_id')
                ->andWhere('iltp.date BETWEEN :minDate AND :maxDate')
                ->setParameter(':planning_id', $planning->getId())
                ->setParameter(':minDate', $minDate)
                ->setParameter(':maxDate', $maxDate)
                ->getQuery()->getResult();
            // on récupère chaque identifiant des charges
            $processingsCharges = [];
            foreach ($processings as $processing) {
                if ($processing->getCharge() instanceof Doctrine\IlotChargeEntity) {
                    $processingsCharges[] = $processing->getCharge()->getId();
                }
            }
            // on sélectionne toutes les charges de départ des îlots du planning
            $qbCharges = $this->getRepository(Doctrine\PlanningIlotEntity::class)->createQueryBuilder('plni');
            $charges = $qbCharges->select('iltc')
                ->leftJoin(
                    Doctrine\IlotChargeEntity::class, 'iltc',
                    Join::WITH, 'iltc.ilot = plni.ilot'
                )
                ->where('plni.planning = :planning_id')
                ->andWhere('iltc.quantity_at BETWEEN :minDate AND :maxDate');
            if (!empty($processingsCharges)) {
                // mais on n'inclus pas les charges de départ si on a des prélèvements
                $charges = $charges->andWhere($qbCharges->expr()->notIn('iltc.id', $processingsCharges));
            }
            $charges = $charges->setParameter(':planning_id', $planning->getId())
                ->setParameter(':minDate', $minDate)
                ->setParameter(':maxDate', $maxDate)
                ->getQuery()->getResult();
            // on mélange les deux tableaux en un seul
            foreach ($charges as $charge) {
                array_push($processings, $charge);
            }
            return $processings;
        } else {
            return $planning->getCharges();
        }
    }


}