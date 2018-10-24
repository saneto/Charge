<?php
namespace Core\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;
use Slim\Http\Body;
use Slim\Views\Twig;

class PermissionException extends SlimException
{
    public function __construct(Twig $twig, ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $response->withStatus(403);

        if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $body = new Body(fopen('php://temp', 'r+'));
            $body->write(json_encode(['error' => "Vous n'avez pas les droits nécessaires pour accéder à cette ressource."]));

            $response = $response
                ->withBody($body)
                ->withHeader('Content-Type', 'application/json; charset=UTF-8');
        } else {
            $response = $twig->render(
                $response,
                "errordocs/permission.twig",
                ['title' => "Droits insuffisants", 'statusCode' => $response->getStatusCode()]
            );
        }

        parent::__construct($request, $response);
    }
}
