<?php
namespace App\Middleware;

use App\Entity\Doctrine\UserEntity;
use Core\Exception\ForbiddenException;
use Core\Exception\PermissionException;
use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouteInterface;
use Zend\Permissions\Acl\Acl;

class AdminRolesMiddleware extends Middleware
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
     * @throws ForbiddenException
     */
    public function __invoke(Request $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if($this->callNext($request, $response)) {
            /**
             * @var Acl $acl
             */
            $acl = $this->container->acl;

            /**
             * @var UserEntity $user
             */
            $user = $this->container->auth->getIdentity();

            // on autorise que les utilisateurs avec le rôle admin
            if ($user->getRole() === "admin"
                || $acl->inheritsRole($user, "admin")
            ) {
                return $next($request, $response);
            }
        }

        throw new PermissionException($this->container->view, $request, $response);
    }

    /**
     * Détermine si la Route actuelle peut continuer son éxécution sans être coupée par le Middleware traversé.
     *
     * @param RouteInterface $Route
     *
     * @return bool
     */
    public function isAllowedRoute(RouteInterface $Route): bool
    {
        return true;
    }
}
