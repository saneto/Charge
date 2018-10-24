<?php
namespace App\Entity\Doctrine;

class UserEntity extends Superclass\SuperclassUserEntity
{
    /**
     * @var string
     */
    protected $avatar = '/tk_css/img/anonym.jpg';

    /**
     * @var string
     */
    protected $role = 'guest';
}
