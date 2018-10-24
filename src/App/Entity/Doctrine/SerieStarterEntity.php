<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class SerieStarterEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var SerieEntity
     */
    protected $serie;

    /**
     * @var int
     */
    protected $starter;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var array
     */
    protected $excluded = ['serie'];

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getStarter();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Assert\Callback('validateStarter'));
    }

    /**
     * @param ExecutionContextInterface $context
     * @param mixed $payload
     */
    public function validateStarter(ExecutionContextInterface $context, $payload)
    {
        $minRange = ($this->getSerie()->getId() * 1000) + 1;
        $maxRange = $minRange + 998;

        if ($this->getStarter() < $minRange || $this->getStarter() > $maxRange) {
            $context->buildViolation("Le n° de BL de démarrage doit être compris entre %min% et %max% (Série %serie_id%)")
                ->atPath('starter')
                ->setParameter('%min%', $minRange)
                ->setParameter('%max%', $maxRange)
                ->setParameter('%serie_id%', $this->getSerie()->getId())
                ->addViolation();
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SerieStarterEntity
     */
    public function setId(int $id): SerieStarterEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return SerieEntity
     */
    public function getSerie(): SerieEntity
    {
        return $this->serie;
    }

    /**
     * @param SerieEntity $serie
     * @return SerieStarterEntity
     */
    public function setSerie(SerieEntity $serie): SerieStarterEntity
    {
        $this->serie = $serie;
        return $this;
    }

    /**
     * @return int
     */
    public function getStarter(): int
    {
        return $this->starter;
    }

    /**
     * @param int $starter
     * @return SerieStarterEntity
     */
    public function setStarter(int $starter): SerieStarterEntity
    {
        $maxRange = ($this->getSerie()->getId() * 1000) + 999;
        if ($starter > $maxRange) {
            $starter = ($this->getSerie()->getId() * 1000) + 1;
        }

        $this->starter = $starter;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     * @return SerieStarterEntity
     */
    public function setCreatedAt(\DateTime $created_at): SerieStarterEntity
    {
        $this->created_at = $created_at;
        return $this;
    }
}