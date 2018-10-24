<?php
namespace App\Entity\Planning;

use Yasumi\Holiday;

class HolidayEventEntity extends PlanningEventEntity
{
    protected $priority = 100;

    protected $editable = false;
    protected $startEditable = false;
    protected $durationEditable = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    public function __construct(Holiday $holiday)
    {
        parent::__construct();

        $this->setTitle($holiday->getName())
            ->setStart($holiday)
            ->setClassName('event-holiday')
            ->setColor(TK_RGB_BLUE);
    }

    /**
     * @return string
     */
    public static function getEventType(): string
    {
        return 'holiday';
    }
}