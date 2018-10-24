<?php
namespace App\Middleware;

use App\Entity\Doctrine\UserEntity;
use App\Exception\AclException;
use App\Provider\AclProvider;
use App\Provider\AuthProvider;
use App\Provider\TwigProvider;
use Core\Exception\ForbiddenException;
use Core\Exception\PermissionException;
use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Interfaces\CollectionInterface;
use Slim\Interfaces\RouteInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;

class AclMiddleware extends Middleware
{
    /**
     * @link https://www.slimframework.com/docs/concepts/middleware.html
     *
     * @param Request           $request
     * @param ResponseInterface $response
     * @param callable          $next
     *
     * @return ResponseInterface
     *
     * @throws PermissionException
     */
    public function __invoke(Request $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $allowed = $this->callNext($request, $response);
//        var_dump($allowed); exit;

        if($allowed) {
            return $next($request, $response);
        }

        throw new PermissionException($this->container->get(TwigProvider::getKey()), $request, $response);
    }

    /**
     * Détermine si la Route actuelle peut continuer son éxécution sans être coupée par le Middleware traversé.
     *
     * @param RouteInterface $Route
     *
     * @return bool
     *
     * @throws AclException
     */
    public function isAllowedRoute(RouteInterface $Route): bool
    {
        $allowed = true;

        /**
         * @var AuthenticationService $auth
         */
        $auth = $this->container->get(AuthProvider::getKey());

        /**
         * @var Acl $acl
         */
        $acl = $this->container->get(AclProvider::getKey());

        if ($auth->hasIdentity()) {
            /**
             * @var UserEntity $user
             */
            $user = $auth->getIdentity();

            $allowed = $acl->isAllowed($user, $Route->getName());
            var_dump('isAllowed route ' . ($user->getRoleId() . " " . $Route->getName() . " " . ($allowed ? "ok" : "nok")));

            if (property_exists($Route, 'parents') && !empty($Route->parents)) {
                foreach ($Route->parents as $parent) {
                    $allowed = $acl->isAllowed($user, $parent);
                    var_dump('isAllowed parent ' . $parent . " " . ($user->getRoleId() . " " . (($allowed ? "ok" : "nok"))));

                    if ($allowed) {
                        return true;
                    }
                }
            }

            if (property_exists($Route, 'roles')) {
                $allowed = false;

                /**
                 * @var CollectionInterface $roles
                 */
                $roles = $Route->roles;

                foreach ($roles->all() as $role) {
                    /*var_dump('isAllowed user ' . ($user->getRoleId() . " " . $Route->getName() . " " . (($acl->isAllowed($user, $Route->getName())) ? "ok" : "nok")));
                    var_dump('inheritsRole ' . ($user->getRoleId() . " " . $role . " " . (($acl->inheritsRole($user, $role) ? "ok" : "nok"))));*/

                    if (!$acl->hasRole($role)) {
                        throw new AclException("Role '{$role}' is undefined. Consider remove it on route '{$Route->getName()}' to avoid security issues");
                    }

                    if ($acl->isAllowed($user, $Route->getName())
                        || $acl->inheritsRole($user, $role)
                    ) {
                        return true;
                    }
                }
            }
        }

        return $allowed;
    }
}
