<?php
namespace App\Middleware;

use Core\Exception\ForbiddenException;
use Core\Middleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Interfaces\RouteInterface;

final class AjaxRequestMiddleware extends Middleware
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
        // si la requête n'est pas du XHR (AJAX) on affiche une erreur 403
        if(!$request->isXhr()) {
            throw new ForbiddenException($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * Détermine si la Route actuelle peut continuer son éxécution sans être coupée par le Middleware traversé.
     *
     * @param  RouteInterface $Route
     * @return bool
     */
    public function isAllowedRoute(RouteInterface $Route): bool
    {
        return true;
    }
}
