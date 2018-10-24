<?php
namespace App\Controller;

use App\Entity\Doctrine\IlotRemainingChargeEntity;
use Core\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends Controller
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        return $this->render($response, 'index');
    }
}
