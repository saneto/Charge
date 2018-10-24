<?php
namespace App\Entity\Doctrine;

class VendorUserEntity extends UserEntity
{
    /**
     * @var string
     */
    protected $avatar = '/tk_css/img/anonym.jpg';

    /**
     * @var string
     */
    protected $role = 'vendor';

    /**
     * @return null|string
     */
    public function getDisplayname(): ?string
    {
        return parent::getDisplayname() . " 👷";
    }
}
