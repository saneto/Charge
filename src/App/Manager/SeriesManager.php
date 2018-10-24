<?php
namespace App\Manager;

use App\Entity\Doctrine\SerieEntity;
use Core\Manager\Manager;

class SeriesManager extends Manager
{
    /**
     * @var string
     */
    protected $entityName = SerieEntity::class;
}
