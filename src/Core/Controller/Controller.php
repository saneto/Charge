<?php
namespace Core\Controller;

use App\Provider;
use Core\Manager\Manager;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Controller implements ControllerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Slim\Views\Twig
     */
    private $view;

    /**
     * @var \Slim\Flash\Messages
     */
    protected $flash;

    /**
     * @var EntityManager
     */
    private $doctrine;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var null|string
     */
    protected $menu_item;

    /**
     * @var array
     */
    private $direct_flash = [];

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    abstract public function indexAction(Request $request, Response $response): ResponseInterface;

    /**
     * @param array ...$args
     */
    public static function dump(...$args)
    {
        $vars = func_get_args();

        foreach ($vars as $var) {
            if ($var instanceof \JsonSerializable) {
                $var = $var->jsonSerialize();
            }

            echo "<pre>";
            echo Debug::dump($var, 4, true, false);
            echo "</pre>";
        }
    }

    /**
     * ControllerInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $this->container['view'];
        $this->flash = $this->container['flash'];

        if(property_exists($this, 'menu_item') && $this->menu_item !== null) {
            $this->setMenuItem($this->menu_item);
        }
    }

    /**
     * @param string $itemName
     */
    protected function setMenuItem(string $itemName): void
    {
        $this->view->getEnvironment()->addGlobal('menu_item', $itemName);
    }

    /**
     * @param ResponseInterface $response
     * @param string            $viewName
     * @param array             $data
     *
     * @return ResponseInterface
     */
    public function render(ResponseInterface $response, string $viewName, array $data = []): ResponseInterface
    {
        /**
         * @var \Slim\Views\Twig $twig
        */
        $viewFile = str_replace('.inc', '%INC', $viewName);
        $viewFile = str_replace('.', '/', $viewFile);
        $viewFile = str_replace('%INC', '.inc', $viewFile) . '.twig';

        // on récupère les flash messages en session
        $session_flash = $this->flash->getMessages();

        if(!empty($session_flash)) {
            foreach($session_flash as $key => $messages) {
                if(!array_key_exists($key, $this->direct_flash)) {
                    $this->direct_flash[$key] = [];
                }

                $this->direct_flash[$key] = array_merge($this->direct_flash[$key], $messages);
            }
        }

        // on injecte les flash messages dans les variables globales pour les afficher
        if(!empty($this->direct_flash)) {
            $this->view->getEnvironment()->addGlobal('flash', $this->direct_flash);
        }

        return $this->view->render($response, $viewFile, $data);
    }

    /**
     * @param Response $response
     * @param string $routeName
     * @param array $args
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public function withRedirect(Response $response, string $routeName, array $args = [], int $statusCode = 302): ResponseInterface
    {
        return $response->withRedirect($this->pathFor($routeName, $args), $statusCode);
    }

    /**
     * @see Router::pathFor()
     *
     * @param $name
     * @param array $data
     * @param array $queryParams
     *
     * @return string
     */
    public function pathFor($name, array $data = [], array $queryParams = []): string
    {
        return $this->container['router']->pathFor($name, $data, $queryParams);
    }

    /**
     * @param Request $request
     * @param string $key
     * @param null $default
     *
     * @return mixed|string
     */
    public function getParsedBodyParam(Request $request, string $key, $default = null)
    {
        $data = $request->getParsedBodyParam($key, $default);

        switch (gettype($data)) {
            case "string":
                return strip_tags($data);
            case "array":
                foreach ($data as $k => $v) {
                    $data[$k] = (is_array($v)) ? array_map('strip_tags', $v) : strip_tags($v);
                }

                return $data;
            default:
                return $data;
        }
    }

    /**
     * @param object $object
     * @param null   $constraints
     * @param null   $groups
     *
     * @return ConstraintViolationListInterface
     */
    public function validate($object, $constraints = null, $groups = null): ConstraintViolationListInterface
    {
        $errors = $this->getValidator()->validate($object, $constraints, $groups);

        return $errors;
    }

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @return null|array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function persist($entity, bool $flush = true): ?array
    {
        $errors = $this->validate($entity);
        if(count($errors) !== 0) {
            return $this->parseValidator($errors);
        }

        $doctrine = $this->getDoctrine();

        $doctrine->persist($entity);
        if($flush) {
            $doctrine->flush();
        }

        return null;
    }

    /**
     * @param ConstraintViolationListInterface $errors
     *
     * @return array
     */
    protected function parseValidator(ConstraintViolationListInterface $errors): array
    {
        $output = [];

        /**
         * @var ConstraintViolationInterface $error
        */
        foreach ($errors as $k => $error) {
            $key = $error->getPropertyPath() ?: $k;

            $output[$key] = [
                'input'   => $error->getInvalidValue(),
                'message' => $error->getMessage()
            ];
        }

        return $output;
    }

    /**
     * @return EntityManager
     */
    protected function getDoctrine(): EntityManager
    {
        if(!$this->doctrine instanceof EntityManager) {
            $this->doctrine = $this->container[Provider\DoctrineProvider::getKey()];
        }

        return $this->doctrine;
    }

    /**
     * @return ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        if(!$this->validator instanceof ValidatorInterface) {
            $this->validator = $this->container[Provider\ValidatorProvider::getKey()];
        }

        return $this->validator;
    }

    /**
     * @param string $managerName
     *
     * @return Manager
     */
    protected function getManager(string $managerName): Manager
    {
        if($this->container->has($managerName) === false) {
            $this->container[$managerName] = function () use ($managerName): Manager {
                return new $managerName($this->getDoctrine());
            };
        }

        return $this->container->get($managerName);
    }

    /**
     * @param string $entityName
     *
     * @return EntityRepository
     */
    protected function getRepository(string $entityName): EntityRepository
    {
        if($this->container->has($entityName) === false) {
            $this->container[$entityName] = function () use ($entityName): EntityRepository {
                return $this->getDoctrine()->getRepository($entityName);
            };
        }

        return $this->container->get($entityName);
    }

    /**
     * Ajout d'un message flash, qui sera affiché au prochaine Header Location.
     * $now détermine s'il faut l'ajouter directement pour l'afficher sans redirection.
     *
     * @param string $key
     * @param mixed  $message
     * @param bool   $now
     */
    protected function addMessage(string $key, $message, bool $now = false): void
    {
        if($now) {
            $old_flash = $this->direct_flash[$key] ?? false;

            if($old_flash !== false) {
                $this->direct_flash[$key] = array_merge($this->direct_flash[$key], [$message]);
            } else {
                $this->direct_flash[$key] = [$message];
            }
        } else {
            $this->flash->addMessage($key, $message);
        }
    }

    /**
     * Ajout d'un message flash direct, qui sera affiché sans avoir à faire un Header Location.
     *
     * @param string  $key
     * @param $message
     */
    protected function addMessageNow(string $key, $message): void
    {
        $this->addMessage($key, $message, true);
    }

    /**
     * @param array $errors
     * @param bool $now
     */
    protected function addValidatorMessages(array $errors, bool $now = false): void
    {
        foreach ($errors as $k => $error) {
            $this->addMessage('danger', $error['message'], $now);
        }
    }
}
