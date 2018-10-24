<?php
/**
 * Created by PhpStorm.
 * User: staginfo02
 * Date: 13/12/2017
 * Time: 14:55
 */

namespace App\Entity\Doctrine;

use App\Entity\Planning\PlanningChargeEventEntity;
use App\Entity\Planning\PlanningProcessingEventEntity;
use Core\Entity\Entity;

class PlanningProcessingEntity extends Entity
{
    protected static $toEvent = false;

    /**
     * @var array
     */
    protected $excluded = ['planning', 'charge'];

    /**
     * @var string
     */
    protected $from_table = 'cdep';

    /**
     * @var null|string
     */
    protected $id;

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
    protected $quantity;

    /**
     * @var null|string
     */
    protected $to_ilots;

    /**
     * @var null|PlanningEntity
     */
    protected $planning;

    /**
     * @var null|PlanningChargeEntity
     */
    protected $charge;

    /**
     * @var bool
     */
    protected $orphan;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var
     */
    protected $url;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getQuantity();
    }

    /**
     * L'objet sera mappé en event FullCalendar
     */
    public static function toEvent(): void
    {
        static::$toEvent = true;
    }

    /**
     * L'objet sera mappé telquel
     */
    public static function toEntiy(): void
    {
        static::$toEvent = false;
    }

    /**
     * @return PlanningChargeEventEntity|PlanningProcessingEventEntity|mixed
     */
    public function jsonSerialize()
    {
        if (self::$toEvent === true) {
            switch ($this->getFromTable()) {
                case "cdep":
                    return new PlanningProcessingEventEntity($this);
                case "plnc":
                    return new PlanningChargeEventEntity($this);
            }
        }

        return parent::jsonSerialize();
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PlanningProcessingEntity
     */
    public function setUrl(string $url): PlanningProcessingEntity
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromTable(): string
    {
        return $this->from_table;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
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
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return null|string
     */
    public function getToIlots()
    {
        return $this->to_ilots;
    }

    /**
     * @return PlanningEntity|null
     */
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * @return PlanningChargeEntity|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @return bool
     */
    public function isOrphan(): bool
    {
        return $this->orphan;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }
}