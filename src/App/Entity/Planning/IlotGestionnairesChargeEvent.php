<?php
/**
 * Created by PhpStorm.
 * User: staginfo02
 * Date: 20/11/2017
 * Time: 15:00
 */

namespace App\Entity\Planning;

use App\Entity\Doctrine\IlotChargeEntity;
use App\Entity\Doctrine\IlotProcessingEntity;
use App\Twig\TwigExtensions;

class IlotGestionnairesChargeEvent extends PlanningEventEntity
{
    /**
     * @var IlotChargeEntity
     */
    private $event;

    protected $editable = false;
    protected $startEditable = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * IlotGestionnairesChargeEvent constructor.
     * @param IlotChargeEntity|IlotProcessingEntity $event
     * @param bool $hydrate
     */
    public function __construct($event, bool $hydrate = true)
    {
        parent::__construct();
        $this->event = $event;

        if($hydrate) {
            $this->_setTitle();
            $start = clone $this->event->getDate();

            if (is_numeric($this->event->getId())) {
                $this->setId($this->event->getId());
            }

            $this->setStart($this->event->getDate())
                ->setEnd($start->add(new \DateInterval('P01D')))
//                ->setColor(TwigExtensions::colorizeQuantityFilter($this->event->getQuantity(), true))
                ->setColor($this->event->getIlot()->getColor())
                ->setTextColor(reverse_color($this->event->getIlot()->getColor()))
                ->setData('quantity', $this->event->getQuantity())
                ->setData('ilot', $this->event->getIlot())
                ->setData('quantity_at', $this->event->getDate());
        }
    }

    private function _setTitle()
    {
        $title = "{$this->event->getIlot()->getName()}: {$this->event->getQuantity()} pièces";
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