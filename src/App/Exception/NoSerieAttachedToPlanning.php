<?php
/**
 * Created by PhpStorm.
 * User: Gaetan
 * Date: 16/12/2017
 * Time: 19:37
 */

namespace App\Exception;

use App\Entity\Doctrine\SerieEntity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;
use Slim\Views\Twig;

class NoSerieAttachedToPlanning extends SlimException
{
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, array $plannings, SerieEntity $serie)
    {
        $response = $twig->render($response, "errordocs/no_serie_attached_to_planning.twig", ['plannings' => $plannings, 'serie' => $serie])
            ->withStatus(500);

        parent::__construct($request, $response);
    }
}
