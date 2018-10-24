<?php
namespace Core\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

interface ControllerInterface
{
    /**
     * ControllerInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param ResponseInterface $response
     * @param string            $viewName
     * @param array             $data
     *
     * @return ResponseInterface
     */
    public function render(ResponseInterface $response, string $viewName, array $data = []): ResponseInterface;

    /**
     * @param Response $response
     * @param string $routeName
     * @param array $args
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public function withRedirect(
        Response $response,
        string $routeName,
        array $args = [],
        int $statusCode = 302
    ): ResponseInterface;
}

