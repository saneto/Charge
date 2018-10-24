<?php
namespace App\Entity\Doctrine;

class PlanningChargeEntity extends PlanningProcessingEntity
{
    /**
     * @var string
     */
    protected $from_table = 'plnc';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var PlanningEntity
     */
    protected $planning;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getQuantity();
    }

    /**
     * @param int $id
     * @return PlanningChargeEntity
     */
    public function setId(int $id): PlanningChargeEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return PlanningEntity
     */
    public function getPlanning(): PlanningEntity
    {
        return $this->planning;
    }

    /**
     * @param PlanningEntity $planning
     * @return PlanningChargeEntity
     */
    public function setPlanning(PlanningEntity $planning): PlanningChargeEntity
    {
        $this->planning = $planning;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return PlanningChargeEntity
     */
    public function setQuantity(int $quantity): PlanningChargeEntity
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return PlanningChargeEntity
     */
    public function setDate(\DateTime $date): PlanningChargeEntity
    {
        $this->date = $date;
        return $this;
    }
}