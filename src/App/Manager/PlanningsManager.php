<?php
namespace App\Manager;

use App\Entity\Doctrine;
use Core\Manager\Manager;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\Expr\Join;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Doctrine\IlotProcessingEntity;
use App\Provider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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
}