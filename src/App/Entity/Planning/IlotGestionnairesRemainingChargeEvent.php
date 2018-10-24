<?php
/**
 * Created by PhpStorm.
 * User: staginfo02
 * Date: 20/11/2017
 * Time: 15:00
 */

namespace App\Entity\Planning;

use App\Entity\Doctrine\IlotRemainingChargeEntity;
use App\Twig\TwigExtensions;

class IlotGestionnairesRemainingChargeEvent extends PlanningEventEntity
{
    /**
     * @var IlotRemainingChargeEntity
     */
    private $event;

    /**
     * @var int
     */
    protected $priority;

    protected $editable = false;
    protected $startEditable = false;

    /**
     * @var string
     */
    protected $tooltip = "Effectuer un prélèvement";

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    public function __construct(IlotRemainingChargeEntity $event, bool $hydrate = true)
    {
        parent::__construct();
        $this->event = $event;

        if($hydrate) {
            $this->_setTitle();
            $start = clone $this->event->getQuantityAt();

            $this->setId($this->event->getId())
                ->setStart($this->event->getQuantityAt())
                ->setEnd($start->add(new \DateInterval('P01D')))
                ->setColor(TwigExtensions::colorizeQuantityFilter($this->event->getRemainingQuantity(), true))
                ->setData('quantity', $this->event->getRemainingQuantity())
                ->setData('quantity_at', $this->event->getQuantityAt())
                ->setData('ilot', $this->event->getIlot());

            $this->priority = $this->event->getRemainingQuantity();
        }
    }

    private function _setTitle()
    {
        $title = "{$this->event->getIlot()->getName()}: {$this->event->getRemainingQuantity()} pièces";
        $this->setTitle($title);
    }

    /**
     * @return string
     */
    public static function getEventType(): string
    {
        return 'chargeGestionnaire';
    }
}