<?php
namespace App\Manager;

use App\Entity\Doctrine\CommandeEntity;
use App\Entity\Doctrine\SerieEntity;
use App\Entity\Doctrine\SerieStarterEntity;
use Core\Manager\Manager;
use Doctrine\ORM\Query\Expr\Join;

class SeriesStartersManager extends Manager
{
    /**
     * @var string
     */
    protected $entityName = SerieStarterEntity::class;

    /**
     * @param SerieEntity $serie
     * @return object|null
     */
    public function getNextSerieStarter(SerieEntity $serie)
    {
        // on prend le n° de BL saisie lors de la création de la Série
        $bls = $this->getRepository(SerieStarterEntity::class)->createQueryBuilder('bls')
            ->where('bls.serie = :serie_id')
            ->orderBy('bls.id', 'DESC')
            ->addOrderBy('bls.created_at', 'DESC');

        $bls->setParameter('serie_id', $serie->getId())
            ->setMaxResults(1);

        $serieStarter = $bls->getQuery()->getOneOrNullResult();
        if ($serieStarter instanceof SerieStarterEntity === false) {
            // si on en n'a pas on part du tout premier de la Série
            $serieStarter = $serie->getFirstStarter();
            $this->getDoctrine()->persist($serie->getStarter());
        }

        // on prend le dernier n° de BL de la Série saisie en commande
        $cde = $this->getRepository(CommandeEntity::class)->createQueryBuilder('cde')
            ->select('s AS serie', 'cde.blId AS starter, cde.createdAt')
            ->leftJoin(
                SerieEntity::class, 's',
                Join::WITH, 's.id = cde.serie'
            )
            ->where('cde.serie = :serie_id')
            ->orderBy('cde.createdAt', 'DESC');

        $cde->setParameter('serie_id', $serie->getId())
            ->setMaxResults(1);

        $commandeStarter = $cde->getQuery()->getOneOrNullResult();
        if ($commandeStarter !== null) {
            $commandeStarter = (new SerieStarterEntity)
                ->setSerie($commandeStarter['serie'])
                ->setStarter($commandeStarter['starter'])
                ->setCreatedAt($commandeStarter['createdAt']);

            if (
                $commandeStarter->getStarter() > $serieStarter->getStarter()
                && $commandeStarter->getCreatedAt() > $serieStarter->getCreatedAt()
            ) {
                $previousStarter = $commandeStarter;
            } else {
                $previousStarter = $serieStarter;
            }
        } else {
            $previousStarter = $serieStarter;
        }

        $nextStarter = (new SerieStarterEntity)
            ->setSerie($serie)
            ->setStarter($previousStarter->getStarter() + 1)
            ->setCreatedAt(new \DateTime());

        $nextStarter = $this->getDoctrine()->merge($nextStarter);
        $this->getDoctrine()->persist($nextStarter);
        $this->getDoctrine()->flush();

        return $nextStarter;
    }
}