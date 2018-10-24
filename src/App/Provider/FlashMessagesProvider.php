<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;
use Slim\Flash\Messages;

class FlashMessagesProvider extends ProviderFactory
{
    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (): Messages {
            return new Messages();
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'flash';
    }
}
