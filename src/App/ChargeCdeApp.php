<?php
namespace App;

use App\Entity\Dev\GaetanSimonEntity;
use App\Entity\Dev\JohnDoeEntity;
use App\Entity\Doctrine\UserEntity;

final class ChargeCdeApp
{
    /**
     * @var null|string
     */
    private static $_env = null;

    /**
     * @return string
     */
    public function __toString()
    {
        return static::getTitle();
    }

    /**
     * @return string
     */
    public static function getEnv(): string
    {
        if (static::$_env === null) {
            static::$_env = (getenv('ENV') ?: 'dev');
        }

        return static::$_env;
    }

    /**
     * @return string
     */
    public static function getTitle(): string
    {
        $title = "charge-cde";

        switch (self::getEnv()) {
            case 'dev':
                $title .= " (dev)";
                break;
            case 'tests':
                $title .= " (tests)";
                break;
        }

        return $title;
    }

    /**
     * @return UserEntity[]
     */
    public static function getDevelopers(): array
    {
        return [
            new GaetanSimonEntity()
        ];
    }
}