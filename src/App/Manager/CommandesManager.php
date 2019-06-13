<?php
namespace App\Manager;

use App\Entity\Doctrine;
use Core\Manager\Manager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Doctrine\CommandeEntity;
use App\Entity\Doctrine\CommandeProcessingEntity;
use App\Entity\Doctrine\DepotEntity;
use App\Entity\Doctrine\IlotEntity;
use App\Entity\Doctrine\UserEntity;

class CommandesManager extends Manager
{
    /**
     * @var string
     */
    protected $entityName = Doctrine\CommandeEntity::class;

    /**
     * @param int $start
     * @param int $max
     *
     * @return Paginator
     */
    public function getPaginatorCommandes(int $start = 0, int $max = 20)
    {
        if ($start > 0) {
            $start = $start * $max;
        }

        $qb = $this->getRepository()->createQueryBuilder('commande')
            ->orderBy('commande.createdAt', 'DESC');

        $qb->setFirstResult($start)
            ->setMaxResults($max);

        return (new Paginator($qb));
    }

    public function getallCommandes()
    {
        return $this->getDoctrine()->createQueryBuilder()
            ->select('ce.dateLancement,
                            ue.displayname, ce.blId, ce.id, 
                            ce.clientName, ce.clientReference, de.name, ce.casType, 
                            ce.machineTs, ce.departTs, ce.sousTraitant,  cpe.quantity, 
                            ce.processingAt, ie.name as ilotname, de.name as depotname, 
                            cpe.processing_at,     ce.deliveryAt ')
            ->from( CommandeEntity::class, 'ce')
            ->leftJoin(
                CommandeProcessingEntity::class, 'cpe',
                Join::WITH, 'cpe.bill = ce.id '
            )->leftJoin(
                UserEntity::class, 'ue',
                Join::WITH, 'ue.id = ce.vendor ')
            ->leftJoin(
                DepotEntity::class, 'de',
                Join::WITH, 'cpe.depot = de.id '
            )->leftJoin(
                IlotEntity::class, 'ie',
                Join::WITH, 'cpe.ilot = ie.id '
            )->groupBy("cpe.id")->getQuery()->getResult();

    }

}
