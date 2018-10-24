<?php
namespace App\Entity\Doctrine;

use App\Twig\TwigExtensions;
use Core\Entity\Entity;

class DepotEntity extends Entity
{
    /**
     * @var int
     */
    const OPENED = 1;

    /**
     * @var int
     */
    const CLOSED = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $open;

    /**
     * @return string
     */
    public function __toString()
    {
        $id = TwigExtensions::zeroleadFilter($this->getId());

        return $id . " " . $this->getName();
    }

    /**
     * @param int $id
     *
     * @return DepotEntity
     */
    public function setId(int $id): DepotEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return DepotEntity
     */
    public function setName(string $name): DepotEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param bool $open
     * @return DepotEntity
     */
    public function setOpen(bool $open): DepotEntity
    {
        $this->open = $open;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->open;
    }
}
