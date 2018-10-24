<?php
namespace App\Manager;

use App\Entity\Doctrine;
use Core\Manager\Manager;
use Doctrine\ORM\Tools\Pagination\Paginator;

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
}