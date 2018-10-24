<?php
namespace Core\Provider;

use Slim\Handlers\Strategies\RequestResponseArgs;

class FoundProvider extends ProviderFactory
{
    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): RequestResponseArgs {
            return new RequestResponseArgs();
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'foundHandler';
    }
}
