<?php
namespace App\Entity\Dev;

use App\Entity\Doctrine;

class JohnDoeEntity extends Doctrine\UserEntity
{
    /**
     * @var string
     */
    protected $role = 'anonymous';

    /**
     * GaetanSimonEntity constructor.
     */
    public function __construct()
    {
        $this->setId("jdoe")
            ->setVmeId("JD")
            ->setDisplayname("John Doe");
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDisplayname() . ' 🕵';
    }
}