<?php
namespace App\Provider;

use App\Exception\AclException;
use App\Middleware;
use Core\Provider\ProviderFactory;
use Core\Utils\AclInitiator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Zend\Authentication\AuthenticationService;

class AclProvider extends ProviderFactory
{
    /**
     * @var array
     */
    private static $_ROLES = [];

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return [
            'permissions' => static::getPermissionsFile()
        ];
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (ContainerInterface $container) {
            $settings = static::getSettings();
            $permissions = $settings['permissions'];

            if(is_file($permissions)) {
                $permissions = Yaml::parse(file_get_contents($permissions));
            } elseif(!is_array($permissions)) {
                throw new AclException("Permissions must be a valid .yml file or a PHP array");
            }

            $acl = (new AclInitiator($permissions))
                ->getAcl();

            // on supprime le rôle dev des rôles autorisés à être donné
            $roles = array_filter($acl->getRoles(), function ($v) {
                return ($v !== "dev");
            });

            static::$_ROLES = $roles;

            /**
             * @var AuthenticationService $auth
            */
            $auth = $container->get(AuthProvider::getKey());
            if($auth->hasIdentity()) {
                $acl->addRole($auth->getIdentity(), $auth->getIdentity()->getRole());
            }

            return $acl;
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'acl';
    }

    /**
     * @return string
     */
    public static function getPermissionsFile(): string
    {
        switch (AppProvider::getEnv()) {
            default:
                return ROOT_DIR . '/permissions.yml';
        }
    }

    public static function getAclMiddleware(): string
    {
        return Middleware\AclMiddleware::class;
    }

    public static function getRoles(): array
    {
        return static::$_ROLES;
    }
}
