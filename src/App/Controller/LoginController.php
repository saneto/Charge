<?php
namespace App\Controller;

use App\Adapter;
use App\Entity\Doctrine\UserEntity;
use Core\Controller\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Zend\Authentication\AuthenticationService;

class LoginController extends Controller
{
    /**
     * @var \phpCAS
     */
    private $phpCAS;

    /**
     * LoginController constructor.
     *
     * @param ContainerInterface $Container
     */
    public function __construct(ContainerInterface $Container)
    {
        parent::__construct($Container);
        $this->phpCAS = $this->container->get('phpcas');
    }

    /**
     * Connexion à l'application via le portail CAS.
     *
     * @deprecated <p>
     *  Connexion établie au niveau de \App\Middleware\LoginCasMiddleware.
     *  Ici on va informer l'utilisateur qu'il est déjà connecté s'il demande /login.
     * </p>
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var AuthenticationService $auth
        */
        $auth = $this->container->get('auth');

        if($auth->hasIdentity()) {
            /**
             * @var UserEntity $user
            */
            $user = $auth->getIdentity();

            $message = "Vous êtes déjà connecté en tant que {$user->getDisplayname()}";
            if($user instanceof Adapter\CasAuthAdapter) {
                $message .= " ({$user->getId()}@tkmf.ad)";
            }

            $this->addMessage('warning', $message);
        }

        return $this->withRedirect($response, 'root');
    }

    /**
     * Déconnexion de l'application via le portail CAS.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function logoutAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var AuthenticationService $auth
        */
        $auth = $this->container->get('auth');

        if($auth->hasIdentity() && ($auth->getAdapter() instanceof Adapter\CasAuthAdapter)) {
            $auth->clearIdentity();

            $phpCAS = $this->phpCAS;
            $phpCAS::logout();

            return $response->withRedirect($phpCAS::getServerLogoutURL(), 302);
        }

        // $this->addMessage('warning', "La déconnexion CAS est désactivée en phase de développement");
        $auth->clearIdentity();

        return $this->withRedirect($response, 'root');
    }
}
