<?php
namespace App\Entity\Planning;

class DepotEventEntity extends PlanningEventEntity
{
    protected $editable = false;
    protected $startEditable = false;
    protected $durationEditable = false;

    public function __construct()
    {
        parent::__construct();
        $this->setColor(TK_RGB_MEDIUM_BLUE);
        $this->setClassName('event-depot');
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
        return 'depot';
    }
}
