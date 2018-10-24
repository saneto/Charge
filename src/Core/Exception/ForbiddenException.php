<?php
namespace Core\Exception;

use Core\Handler\ForbiddenHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;

class ForbiddenException extends SlimException
{
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = (new ForbiddenHandler)($request, $response);

        parent::__construct($request, $response);
    }
}
