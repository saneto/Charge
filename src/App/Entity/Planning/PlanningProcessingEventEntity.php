<?php
namespace App\Entity\Planning;

use App\Entity\Doctrine\PlanningProcessingEntity;
use App\Twig;

final class PlanningProcessingEventEntity extends PlanningEventEntity
{
    /**
     * @var PlanningProcessingEntity
     */
    private $processing;
    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * PlanningProcessingEventEntity constructor.
     * @param PlanningProcessingEntity $processing
     */
    public function __construct(PlanningProcessingEntity $processing)
    {
        parent::__construct();
        $this->processing = $processing;

        $title = "Prlv: {$processing->getCommandesQuantity()}";
        $title .= " | Rst. {$processing->getQuantity()}/{$processing->getOriginalQuantity()}";

        $color = Twig\TwigExtensions::colorizeQuantityFilter($processing->getQuantity(), true);
        $end = clone $processing->getDate();
        $end->add(new \DateInterval('P01D'));

        $this->setTitle($title)
            ->setColor($color)
            ->setEditable(false)
            ->setOverlap(false)
            ->setUrl($processing->getUrl())
            ->setStart($processing->getDate())
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
        return "chargeDetails";
    }
}