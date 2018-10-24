<?php
namespace Reference;

use Core\Entity\Entity;
use InterfaceManager\IPlanningsManager;

class PlanningsManager implements IPlanningsManager
{

    public function getEntity()
    {
        
    }
   
    private function calldatabase()
    {
        
    }
    public function getEntityWithEntityAndArray(array $array, Entity $entity)
    {
            $oneDayBefore = new \DateInterval('P01D');
            $oneDayBefore->invert = 1;
            
            $minDate->add($oneDayBefore);
            
            /**
             * On récupère le totla des prélèvements pour les îlots attachés au planning.
             * @var Doctrine\IlotProcessingEntity[] $processings
             */
            $processings = $this->getRepository()->createQueryBuilder('iltp')
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
    }

    public function getEntityWithEntity(Entity $entity)
    {}

    public function getEntityWithID(int $id)
    {}

    public function getEntityWithArray(array $array)
    {}


   

}

