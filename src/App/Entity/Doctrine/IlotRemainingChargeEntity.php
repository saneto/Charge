<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;

class IlotRemainingChargeEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var null|int
     */
    protected $charge;

    /**
     * @var IlotEntity
     */
    protected $ilot;

    /**
     * @var int
     */
    protected $original_quantity;

    /**
     * @var int
     */
    protected $commandes_quantity;

    /**
     * @var int
     */
    protected $remaining_quantity;

    /**
     * @var \DateTime
     */
    protected $quantity_at;

    /**
     * @var string
     */
    protected $from_table;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getRemainingQuantity();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|int
     */
    public function getCharge(): ?int
    {
        return $this->charge;
    }

    /**
     * @return IlotEntity
     */
    public function getIlot(): IlotEntity
    {
        return $this->ilot;
    }

    /**
     * @return int
     */
    public function getOriginalQuantity(): int
    {
        return $this->original_quantity;
    }

    /**
     * @return int
     */
    public function getCommandesQuantity(): int
    {
        return $this->commandes_quantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity(): int
    {
        return $this->remaining_quantity;
    }

    /**
     * @return \DateTime
     */
    public function getQuantityAt(): \DateTime
    {
        return $this->quantity_at;
    }

    /**
     * @return string
     */
    public function getFromTable(): string
    {
        return $this->from_table;
    }
}