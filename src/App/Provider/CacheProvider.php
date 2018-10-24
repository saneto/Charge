<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;

class CacheProvider extends ProviderFactory
{
    /**
     * @return string
     */
    static function getKey(): string
    {
        return 'cache';
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): \Slim\HttpCache\CacheProvider {
            return new \Slim\HttpCache\CacheProvider();
        };
    }
}