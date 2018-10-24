<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;

class PlanningIlotEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var PlanningEntity|null
     */
    protected $planning;

    /**
     * @var IlotEntity|null
     */
    protected $ilot;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
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
     * @return PlanningIlotEntity
     */
    public function setId(int $id): PlanningIlotEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return PlanningEntity|null
     */
    public function getPlanning(): ?PlanningEntity
    {
        return $this->planning;
    }

    /**
     * @param PlanningEntity|null $planning
     * @return PlanningIlotEntity
     */
    public function setPlanning(?PlanningEntity $planning): PlanningIlotEntity
    {
        $this->planning = $planning;
        return $this;
    }

    /**
     * @return IlotEntity|null
     */
    public function getIlot(): ?IlotEntity
    {
        return $this->ilot;
    }

    /**
     * @param IlotEntity|null $ilot
     * @return PlanningIlotEntity
     */
    public function setIlot(?IlotEntity $ilot): PlanningIlotEntity
    {
        $this->ilot = $ilot;
        return $this;
    }
}