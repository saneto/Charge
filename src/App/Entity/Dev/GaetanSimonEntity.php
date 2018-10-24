<?php
namespace App\Entity\Dev;

use App\Entity\Doctrine;

class GaetanSimonEntity extends Doctrine\UserEntity
{
    /**
     * @var string
     */
    protected $avatar = 'img/dev/gaetansimon1.png';

    /**
     * @var string
     */
    protected $role = 'dev';

    /**
     * GaetanSimonEntity constructor.
     */
    public function __construct()
    {
        $this->setId(10485311)
            ->setVmeId("GS")
            ->setDisplayname("Gaëtan Simon");
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDisplayname() . ' 🤓';
    }
}