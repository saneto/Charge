<?php
namespace App\Entity\Doctrine;

use App\Entity\Planning;
use Core\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class IlotChargeEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /***
     * @var IlotEntity
     */
    protected $ilot;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var \DateTime
     */
    protected $quantity_at;

    /**
     * @var
     */
    private $eventUrl;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints(
            'quantity', [
                new Assert\GreaterThanOrEqual(
                    [
                        'value' => 0,
                        'message' => "La quantité minimale doit être de 0 pièce"
                    ]
                )
            ]
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getQuantity();
    }

    public function jsonSerialize()
    {
        if (self::$to_event === true) {
            return (new Planning\IlotChargeEvent($this));
        }

        return parent::jsonSerialize();
    }

    /**
     * @param int $id
     *
     * @return IlotChargeEntity
     */
    public function setId(int $id): IlotChargeEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param IlotEntity $ilot
     *
     * @return IlotChargeEntity
     */
    public function setIlot(IlotEntity $ilot): IlotChargeEntity
    {
        $this->ilot = $ilot;

        return $this;
    }

    /**
     * @return IlotEntity
     */
    public function getIlot(): IlotEntity
    {
        return $this->ilot;
    }

    /**
     * @param int $quantity
     *
     * @return IlotChargeEntity
     */
    public function setQuantity(int $quantity): IlotChargeEntity
    {
        $this->quantity = $quantity;

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
     * @param \DateTime $quantity_at
     *
     * @return IlotChargeEntity
     */
    public function setQuantityAt(\DateTime $quantity_at): IlotChargeEntity
    {
        $this->quantity_at = $quantity_at;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getQuantityAt(): \DateTime
    {
        if($this->quantity_at === null) {
            $this->setQuantityAt(new \DateTime());
        }

        return $this->quantity_at;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->getQuantityAt();
    }

    /**
     * @param string $url
     * @return IlotChargeEntity
     */
    public function setEventUrl(string $url): IlotChargeEntity
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
