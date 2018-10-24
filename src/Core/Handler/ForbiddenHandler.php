<?php
namespace Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;

class ForbiddenHandler implements HandlerInterface
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
        $body->write("403 Forbidden");

        return $response->withBody($body)
            ->withStatus(403)
            ->withHeader('Content-Type', 'text/plain; charset=UTF-8');
    }
}
