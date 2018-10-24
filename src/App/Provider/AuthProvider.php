<?php
namespace App\Provider;

use App\Adapter;
use App\Middleware;
use App\Provider;
use Core\Provider\ProviderFactory;
use Psr\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;

class AuthProvider extends ProviderFactory
{
    public static function getSettings(): array
    {
        return [
            'adapter'    => static::getAuthAdapter(),
            'middleware' => static::getAuthMiddleware()
        ];
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'auth';
    }

    /**
     * @return callable
     */
    public function getCallable() : callable
    {
        return function (ContainerInterface $container): AuthenticationService {
            $auth = new AuthenticationService();
            $adapter = self::getAuthAdapter();

            // Adadpter si on est en phase de dev
            if ($adapter === Adapter\DevAuthAdapter::class) {
                $auth->setAdapter(new $adapter);
            } else {
                $cas = $container->get(Provider\CasProvider::getKey());
                $doctrine = $container->get(Provider\DoctrineProvider::getKey());

                $auth->setAdapter(new $adapter($cas, $doctrine));
            }

            return $auth;
        };
    }

    /**
     * @return string
     */
    public static function getAuthAdapter(): string
    {
        switch (self::getAuthMiddleware()) {
            case Middleware\LoginDevMiddleware::class:
                return Adapter\DevAuthAdapter::class;
            default:
                return Adapter\CasAuthAdapter::class;
        }
    }

    /**
     * @return string
     */
    public static function getAuthMiddleware(): string
    {
        switch (AppProvider::getEnv()) {
            case 'dev':
                return Middleware\LoginDevMiddleware::class;
            default:
                return Middleware\LoginCasMiddleware::class;
        }
    }
}
