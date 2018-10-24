<?php
namespace App\Entity\Planning;

use Core\Entity\Entity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

abstract class PlanningEventEntity extends Entity
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var bool
     */
    protected $allDay = true;

    /**
     * @var \DateTime
     */
    protected $start;

    /**
     * @var \DateTime
     */
    protected $end;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var bool
     */
    protected $editable = true;

    /**
     * @var bool
     */
    protected $startEditable = true;

    /**
     * @var bool
     */
    protected $durationEditable = false;

    /**
     * @var bool
     */
    protected $ressourceEditable;

    /**
     * @var bool
     */
    protected $overlap = true;

    /**
     * @var mixed
     */
    protected $constraint;

    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var string
     */
    protected $backgroundColor;

    /**
     * @var string
     */
    protected $borderColor;

    /**
     * @var string
     */
    protected $textColor;

    /**
     * @var array
     */
    protected $data;

    /**
     * Attribut title de l'élément jQuery.
     * @var null|string
     */
    protected $tooltip = null;

    /**
     * @var string
     */
    protected $eventType;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new NotBlank());
    }

    /**
     * @return string
     */
    abstract public static function getEventType(): string;

    /**
     * PlanningEventEntity constructor.
     */
    public function __construct()
    {
        $this->eventType = static::getEventType();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $event = array_filter(
            parent::jsonSerialize(),
            function ($value) {
                return ($value !== null);
            }
        );

        return $event;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlanningEventEntity
     */
    public function setId(int $id): PlanningEventEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return PlanningEventEntity
     */
    public function setTitle(string $title): PlanningEventEntity
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    /**
     * @param bool $allDay
     * @return PlanningEventEntity
     */
    public function setAllDay(bool $allDay): PlanningEventEntity
    {
        $this->allDay = $allDay;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        if ($this->start === null) {
            $this->setStart(new \DateTime());
        }

        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return PlanningEventEntity
     */
    public function setStart(\DateTime $start): PlanningEventEntity
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        if ($this->end === null) {
            $end = clone $this->getStart();
            $this->setEnd($end->add(new \DateInterval('P01D')));
        }

        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return PlanningEventEntity
     */
    public function setEnd(\DateTime $end): PlanningEventEntity
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PlanningEventEntity
     */
    public function setUrl(string $url): PlanningEventEntity
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return PlanningEventEntity
     */
    public function setClassName(string $className): PlanningEventEntity
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEditable(): ?bool
    {
        return $this->editable;
    }

    /**
     * @param bool $editable
     * @return PlanningEventEntity
     */
    public function setEditable(bool $editable): PlanningEventEntity
    {
        $this->editable = $editable;

        if ($editable === false) {
            $this->setStartEditable(false)
                ->setDurationEditable(false);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isStartEditable(): ?bool
    {
        return $this->startEditable;
    }

    /**
     * @param bool $startEditable
     * @return PlanningEventEntity
     */
    public function setStartEditable(bool $startEditable): PlanningEventEntity
    {
        $this->startEditable = $startEditable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDurationEditable(): ?bool
    {
        return $this->durationEditable;
    }

    /**
     * @param bool $durationEditable
     * @return PlanningEventEntity
     */
    public function setDurationEditable(bool $durationEditable): PlanningEventEntity
    {
        $this->durationEditable = $durationEditable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRessourceEditable(): ?bool
    {
        return $this->ressourceEditable;
    }

    /**
     * @param bool $ressourceEditable
     * @return PlanningEventEntity
     */
    public function setRessourceEditable(bool $ressourceEditable): PlanningEventEntity
    {
        $this->ressourceEditable = $ressourceEditable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOverlap(): ?bool
    {
        return $this->overlap;
    }

    /**
     * @param bool $overlap
     * @return PlanningEventEntity
     */
    public function setOverlap(bool $overlap): PlanningEventEntity
    {
        $this->overlap = $overlap;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @param mixed $constraint
     * @return PlanningEventEntity
     */
    public function setConstraint($constraint)
    {
        $this->constraint = $constraint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     * @return PlanningEventEntity
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return PlanningEventEntity
     */
    public function setColor(string $color): PlanningEventEntity
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     * @return PlanningEventEntity
     */
    public function setBackgroundColor(string $backgroundColor): PlanningEventEntity
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getBorderColor(): ?string
    {
        return $this->borderColor;
    }

    /**
     * @param string $borderColor
     * @return PlanningEventEntity
     */
    public function setBorderColor(string $borderColor): PlanningEventEntity
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * @return string
     */
    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    /**
     * @param string $textColor
     * @return PlanningEventEntity
     */
    public function setTextColor(string $textColor): PlanningEventEntity
    {
        $this->textColor = $textColor;
        return $this;
    }

    /**
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return PlanningEventEntity
     */
    public function setData(string $key, $value): PlanningEventEntity
    {
        if ($value instanceof \DateTime) {
            $value = $value->format(DATE_ISO8601);
        }

        $this->data[$key] = $value;
        return $this;
    }
}
