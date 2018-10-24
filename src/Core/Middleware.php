<?php
namespace Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;

/**
 * Class Middleware
 *
 * @package Core
 */
abstract class Middleware
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @link https://www.slimframework.com/docs/concepts/middleware.html
     *
     * @param Request           $request
     * @param ResponseInterface $response
     * @param callable          $next
     *
     * @return ResponseInterface
     */
    abstract public function __invoke(Request $request, ResponseInterface $response, callable $next): ResponseInterface;

    /**
     * Détermine si la Route actuelle peut continuer son éxécution sans être coupée par le Middleware traversé.
     *
     * @param RouteInterface $Route
     *
     * @return bool
     */
    abstract public function isAllowedRoute(RouteInterface $Route): bool;

    /**
     * Middleware constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ResponseInterface $response
     * @param string            $routeName
     * @param array             $args
     *
     * @return ResponseInterface
     */
    public function withRedirect(ResponseInterface $response, $routeName, array $args = []): ResponseInterface
    {
        /**
         * @var RouterInterface $Router
        */
        $Router = $this->container->get('router');
        $routePath = $Router->pathFor($routeName, $args);

        return $response->withHeader('Location', $routePath)->withStatus(302);
    }

    /**
     * @param Request           $request
     * @param ResponseInterface $response
     *
     * @return bool
     *
     * @throws NotFoundException
     */
    public function callNext(Request $request, ResponseInterface $response): bool
    {
        /**
         * @var RouteInterface $Route
        */
        $Route = $request->getAttribute('route');

        if(!($Route instanceof RouteInterface)) {
            throw new NotFoundException($request, $response);
        }

        return $this->isAllowedRoute($Route);
    }
}
