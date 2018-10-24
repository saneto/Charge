<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;

class CommentTypeEntity extends Entity
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return CommentTypeEntity
     */
    public function setType(string $type): CommentTypeEntity
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return CommentTypeEntity
     */
    public function setLabel(string $label): CommentTypeEntity
    {
        $this->label = $label;
        return $this;
    }
}