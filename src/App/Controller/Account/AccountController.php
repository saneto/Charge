<?php
namespace App\Controller\Account;

use App\Provider;
use App\Entity\Doctrine\UserEntity;
use Core\Controller\Controller;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AccountController extends Controller
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        return $response->write("En cours de développement ...");
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function addVmeIdAction(Request $request, Response $response): ResponseInterface
    {
        // on envoi le formulaire
        if ($request->isPut()) {
            /**
             * @var UserEntity $user
             */
            $user = $this->container->auth->getIdentity();

            $vme_id = $request->getParsedBodyParam('vme_id', null);

            try {
                $user->setVmeId($vme_id);
                $user = $this->getDoctrine()->merge($user);

                $errors = $this->persist($user);
                if ($errors === null) {
                    $this->addMessage('success', "Votre Code Vendeur a bien été défini à {$user->getVmeId()}");
                    return $this->withRedirect($response, 'root');
                } else {
                    $this->addValidatorMessages($errors, true);
                }
            } catch (\TypeError $e) {
                $message = "Veuilez vérifier les données saisies dans le formulaire";

                if (Provider\AppProvider::getEnv() === "dev") {
                    $message .= " " . $e->getMessage();
                }

                $this->addMessageNow('warning', $message);
            } catch (UniqueConstraintViolationException $e) {
                $vendeur = $this->getRepository(UserEntity::class)->findOneBy(['vme_id' => $vme_id]);
                $this->addMessageNow('warning', "Ce Code Vendeur est déjà utilisé par {$vendeur}");
            }
        }

        $response = $response->withStatus(403);
        return $this->render($response, 'accounts.add_vme_id');
    }
}
