<?php
namespace App\Provider;

use App\ChargeCdeApp;
use Core\Provider\ProviderFactory;

class AppProvider extends ProviderFactory
{
    /**
     * @return array
     */
    public static function getSettings(): array
    {
        $env = static::getEnv();

        return [
            'env' => $env,
            'displayErrorDetails' => ($env === 'dev'),
            'determineRouteBeforeAppMiddleware' => true,
            'routerCacheFile' => static::getRouterCacheFile()
        ];
    }

    /**
     * @return ChargeCdeApp|callable
     */
    public function getCallable(): callable
    {
        return function () {
            return new ChargeCdeApp();
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'app';
    }

    /**
     * @return string
     */
    public static function getEnv(): string
    {
        return ChargeCdeApp::getEnv();
    }

    /**
     * @return false|string
     */
    public static function getRouterCacheFile()
    {
        switch (static::getEnv()) {
            case 'dev':
                return false;
            default:
                return ROOT_DIR . '/routes.yml.php';
        }
    }
}