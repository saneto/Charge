<?php
namespace App\Entity\Planning;

use App\Entity\Doctrine;
use App\Twig\TwigExtensions;

final class IlotProcessingEvent extends PlanningEventEntity
{
    protected $eventType = 'chargeDetails';

    /**
     * IlotProcessingEvent constructor.
     * @param Doctrine\IlotProcessingEntity $processing
     */
    public function __construct(Doctrine\IlotProcessingEntity $processing)
    {
        $title = $processing->getQuantity() . " pièce" . (($processing->getQuantity() < 1 || $processing->getQuantity() > 1) ? "s" : null);

        $end = (clone $processing->getDate())
            ->add(new \DateInterval('P01D'));

        $this->setTitle($title)
            ->setColor($processing->getIlot()->getColor())
            ->setTextColor(reverse_color($processing->getIlot()->getColor()))
            ->setData('ilot', $processing->getIlot())
            ->setData('quantity', $processing->getQuantity())
            ->setUrl($processing->getEventUrl())
            ->setStart($processing->getDate())
            ->setEnd($end);

        if (TwigExtensions::colorizeQuantityFilter($processing->getQuantity()) === "low") {
            $this->setBorderColor(TK_RGB_RED);
        }
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
        return 'chargeDetails';
    }
}