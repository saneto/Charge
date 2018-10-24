<?php
namespace App\Middleware;

use App\Exception\CasAuthException;
use App\Adapter;
use App\Provider;
use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouteInterface;
use Zend\Authentication\AuthenticationService;

final class LoginCasMiddleware extends Middleware
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
        // la Route requière d'être connecté avant de continuer
        if(!$this->callNext($request, $response)) {
            /**
             * @var AuthenticationService $auth
            */
            $auth = $this->container->get('auth');

            if($auth->hasIdentity() === false) {
                try {
                    $result = $auth->authenticate();

                    if ($result->getCode() === Adapter\CasAuthAdapter::MISSING_VME_ID) {
                        return $this->withRedirect($response, 'accounts.add_vme_id');
                    }
                } catch (CasAuthException $e) {
                    /**
                     * @var \phpCAS $phpCAS
                     */
                    $phpCAS = $this->container->get(Provider\CasProvider::getKey());
                    $phpCAS::forceAuthentication();

                    return $response
                        ->withStatus(302)
                        ->withHeader('Location', $phpCAS::getServerLoginURL());
                }
            } elseif ($auth->getIdentity()->getVmeId() === null) {
                $response = $this->withRedirect($response, 'accounts.add_vme_id');
            }
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
        /**
         * @var array $allowed Noms des routes autorisées à traverser le Middleware
        */
        $allowed = ['logout'];

        return (in_array($Route->getName(), $allowed));
    }

}
