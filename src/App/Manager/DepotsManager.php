<?php
namespace App\Manager;

use App\Entity\Doctrine\DepotEntity;
use Core\Manager\Manager;

class DepotsManager extends Manager
{
    /***
     * @var string
     */
    protected $entityName = DepotEntity::class;
}