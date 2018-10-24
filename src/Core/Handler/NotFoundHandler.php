<?php
namespace Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;

class NotFoundHandler implements HandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write("404 Not Found");

        return $response->withBody($body)
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
