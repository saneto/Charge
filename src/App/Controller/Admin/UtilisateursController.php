<?php
namespace App\Controller\Admin;

use App\Entity\Dev\GaetanSimonEntity;
use App\Entity\Doctrine;
use App\Provider\AclProvider;
use App\Provider\AppProvider;
use Core\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class UtilisateursController extends Controller
{
    /**
     * @var string
     */
    protected $menu_item = 'utilisateurs';

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        /**
         * @var Doctrine\UserEntity[] $users
         */
        $users = $this->getRepository(Doctrine\UserEntity::class)->findAll();

        $usersByRole = [];
        foreach ($users as $user) {
            $usersByRole[$user->getRole()][] = $user;
        }

        ksort($usersByRole);
        return $this->render($response, 'admin.utilisateurs.index', [
            'users' => $usersByRole,
            'roles' => AclProvider::getRoles()
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateRoleAction(Request $request, Response $response): ResponseInterface
    {
        $users_id = $request->getParsedBodyParam('users', null);
        if (count($users_id) === 0) {
            return $response->withStatus(204);
        }

        /**
         * @var Doctrine\UserEntity[] $users
         */
        $users = $this->getRepository(Doctrine\UserEntity::class)->findBy(['id' => $users_id]);

        if (count($users) === 0) {
            throw new NotFoundException($request, $response);
        }

        /**
         * @var Doctrine\UserEntity $me
         */
        $me = $this->container->auth->getIdentity();

        $errors = [];
        $updateRole = strip_tags($request->getParsedBodyParam('role', null));

        // on prépare une requête PDO (Doctrine empêche la modification du discriminator)
        $query = $this->getDoctrine()->getConnection()
            ->prepare('UPDATE `users` SET `users`.`role` = :role WHERE `users`.`id` = :user_id');

        foreach ($users as $user) {
            if ($user->getId() == $me->getId()) { // on ne se modifie pas nous même
                $errors['warning'][] = "Vous ne pouvez pas modifier vos droits";
                continue;
            } elseif ($user instanceof GaetanSimonEntity) { // on ne touche pas au développeur
                $errors['warning'][] = "{$user} ne veut pas qu'on puisse modifier ses droits";
                continue;
            }

            // on manipule l'utilisateur pour ajouter son rôle
            try {
                if ($user->getRole() !== $updateRole) {
                    $user->setRole($updateRole);
                    $userErrors = $this->validate($user);

                    if ($userErrors->count() === 0) {
                        // bypass de Doctrine et utilisation de PDO pour envoyer la requête
                        $query->execute([':role' => $user->getRole(), ':user_id' => $user->getId()]);
                        $query->closeCursor();

                        $errors['success'][] = "Les droits de {$user} ont été défini à {$user->getRole()}";
                    } else {
                        $errors['danger'][] = "Les droits de {$user} n'ont pas été modifié suite à une erreur";
                    }
                } else {
                    $errors['success'][] = "{$user} possède déjà le rôle {$user->getRole()}";
                }
            } catch (\TypeError $e) {
                $message = "Veuillez vérifier les données saisies dans le formulaire";

                if (AppProvider::getEnv() == "dev") {
                    $message .= " " . $e->getMessage();
                }

                return $response->withJson(['error' => $message], 500);
            }
        }

        return (!empty($errors)) ? $response->withJson(['errors' => $errors]) : $response->withStatus(204);
    }
}