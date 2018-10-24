<?php
namespace App\Entity\Planning;

use App\Entity\Doctrine\PlanningChargeEntity;

final class PlanningChargeEventEntity extends PlanningEventEntity
{
    /**
     * @var PlanningChargeEntity
     */
    private $charge;

    /**
     * @var int
     */
    protected $priority = 90;

    /**
     * PlanningChargeEventEntity constructor.
     * @param PlanningChargeEntity $charge
     */
    public function __construct(PlanningChargeEntity $charge)
    {
        parent::__construct();
        $this->charge = $charge;

        list($prefix, $id) = explode(':', base64_decode($charge->getId()));
        $end = clone $charge->getDate();
        $end->add(new \DateInterval('P01D'));

        $this->setId($id)
            ->setTitle($charge->getQuantity() . " pièces")
            ->setColor($charge->getPlanning()->getColor())
            ->setClassName('event-charge')
            ->setStart($charge->getDate())
            ->setEnd($end);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public static function getEventType(): string
    {
        return 'charge';
    }
}