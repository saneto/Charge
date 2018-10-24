<?php
namespace App\Entity\Planning;

use App\Entity\Doctrine\IlotChargeEntity;

class IlotChargeEvent extends PlanningEventEntity
{
    /**
     * @var IlotChargeEntity
     */
    private $charge;

    protected $eventType = 'charge';
    protected $tooltip = "Modifier cette dispo.";

    /**
     * @return string
     */
    public static function getEventType(): string
    {
        return "charge";
    }

    /**
     * IlotEvent constructor.
     *
     * @param IlotChargeEntity $charge
     */
    public function __construct(IlotChargeEntity $charge)
    {
        parent::__construct();

        $title = "{$charge->getQuantity()} pièce" . ($charge->getQuantity() > 1 ? "s" : null);
        $color = $charge->getIlot()->getColor();
        $textColor = reverse_color($color);

        $this->setId($charge->getId())
            ->setTitle($title)
            ->setColor($color)
            ->setTextColor($textColor)
            ->setClassName('event-charge')
            ->setData('ilot', $charge->getIlot())
            ->setData('quantity', $charge->getQuantity())
            ->setStart($charge->getQuantityAt());

        if ($charge->getEventUrl() !== null) {
            $this->setUrl($charge->getEventUrl());
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
