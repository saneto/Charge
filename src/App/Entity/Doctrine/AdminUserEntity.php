<?php
namespace App\Entity\Doctrine;

class AdminUserEntity extends UserEntity
{
    /**
     * @var string
     */
    protected $avatar = '/tk_css/img/anonym.jpg';

    /**
     * @var string
     */
    protected $role = 'admin';

    /**
     * @return null|string
     */
    public function getDisplayname(): ?string
    {
        return parent::getDisplayname() . " 👮";
    }
}
