<?php
namespace Core\Manager;

use App\Entity\SerieEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class Manager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * Repository constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getDoctrine(): EntityManager
    {
        return $this->em;
    }

    /**
     * @param string $managerName
     *
     * @return Manager
     */
    public function getManager($managerName): Manager
    {
        return new $managerName($this->em);
    }

    /**
     * @param null|string $entity
     *
     * @return EntityRepository
     */
    public function getRepository(?string $entity = null): EntityRepository
    {
        return $this->em->getRepository($entity ?? $this->entityName);
    }

    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     *
     * @return null|object
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->getRepository()->find($id, $lockMode, $lockVersion);
    }

    /**
     * @see EntityRepository::findBy()
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }
}
