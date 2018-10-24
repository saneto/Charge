<?php
namespace Core\Provider;

use Core\Handler\NotFoundHandler;

class NotFoundProvider extends ProviderFactory
{
    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): NotFoundHandler {
            return new NotFoundHandler();
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'notFoundHandler';
    }
}
