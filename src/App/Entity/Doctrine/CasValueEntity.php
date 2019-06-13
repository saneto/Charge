<?php
/**
 * Created by PhpStorm.
 * User: 10601450
 * Date: 05/12/2018
 * Time: 11:27
 */

namespace App\Entity\Doctrine;

use Core\Entity\Entity;

class CasValueEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $numCAS;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getNumCAS(): int
    {
        return $this->numCAS;
    }

    /**
     * @param int $numCAS
     */
    public function setNumCAS(int $numCAS): void
    {
        $this->numCAS = $numCAS;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}