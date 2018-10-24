<?php
namespace App\Entity\Doctrine;

use App\Entity\Planning;
use Core\Entity\Entity;

class IlotProcessingEntity extends Entity
{
    /**
     * @var string
     */
    protected static $from_table = "cdep";

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $records_ids;

    /**
     * @var PlanningEntity|null
     */
    protected $planning;

    /**
     * @var IlotChargeEntity|null
     */
    protected $charge;

    /**
     * @var IlotEntity|null
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
    protected $quantity;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var bool
     */
    protected $orphan;

    /**
     * @var string
     */
    private $eventUrl;

    /**
     * @return string
     */
    public static function fromTable(): string
    {
        return static::$from_table;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        if (self::$to_event === true) {
            return (new Planning\IlotProcessingEvent($this));
        }

        return parent::jsonSerialize();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getQuantity();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return IlotProcessingEntity
     */
    public function setId(string $id): IlotProcessingEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecordsIds(): string
    {
        return $this->records_ids;
    }

    /**
     * @param string $records_ids
     * @return IlotProcessingEntity
     */
    public function setRecordsIds(string $records_ids): IlotProcessingEntity
    {
        $this->records_ids = $records_ids;
        return $this;
    }

    /**
     * @return PlanningEntity|null
     */
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * @param PlanningEntity|null $planning
     * @return IlotProcessingEntity
     */
    public function setPlanning($planning)
    {
        $this->planning = $planning;
        return $this;
    }

    /**
     * @return IlotChargeEntity|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @param IlotChargeEntity|null $charge
     * @return IlotProcessingEntity
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;
        return $this;
    }

    /**
     * @return IlotEntity|null
     */
    public function getIlot()
    {
        return $this->ilot;
    }

    /**
     * @param IlotEntity|null $ilot
     * @return IlotProcessingEntity
     */
    public function setIlot($ilot)
    {
        $this->ilot = $ilot;
        return $this;
    }

    /**
     * @return int
     */
    public function getOriginalQuantity(): int
    {
        return $this->original_quantity;
    }

    /**
     * @param int $original_quantity
     * @return IlotProcessingEntity
     */
    public function setOriginalQuantity(int $original_quantity): IlotProcessingEntity
    {
        $this->original_quantity = $original_quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getCommandesQuantity(): int
    {
        return $this->commandes_quantity;
    }

    /**
     * @param int $commandes_quantity
     * @return IlotProcessingEntity
     */
    public function setCommandesQuantity(int $commandes_quantity): IlotProcessingEntity
    {
        $this->commandes_quantity = $commandes_quantity;
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
     * @return IlotProcessingEntity
     */
    public function setQuantity(int $quantity): IlotProcessingEntity
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
     * @return IlotProcessingEntity
     */
    public function setDate(\DateTime $date): IlotProcessingEntity
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOrphan(): bool
    {
        return $this->orphan;
    }

    /**
     * @param bool $orphan
     * @return IlotProcessingEntity
     */
    public function setOrphan(bool $orphan): IlotProcessingEntity
    {
        $this->orphan = $orphan;
        return $this;
    }

    /**
     * @param string $url
     * @return IlotProcessingEntity
     */
    public function setEventUrl(string $url): IlotProcessingEntity
    {
        $this->eventUrl = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEventUrl(): ?string
    {
        return $this->eventUrl;
    }
}