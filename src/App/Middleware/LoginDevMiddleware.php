<?php
namespace App\Middleware;

use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouteInterface;
use Zend\Authentication\AuthenticationService;

class LoginDevMiddleware extends Middleware
{
    /**
     * @link https://www.slimframework.com/docs/concepts/middleware.html
     *
     * @param Request           $request
     * @param ResponseInterface $response
     * @param callable          $next
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        /**
         * @var AuthenticationService $auth
        */
        $auth = $this->container->get('auth');

        if($auth->hasIdentity() === false) {
            $auth->authenticate();
            $this->container->get('flash')->addMessage('success', "Connexion avec le compte de développement");

            return $this->withRedirect($response, 'root');
        }

        return $next($request, $response);
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
