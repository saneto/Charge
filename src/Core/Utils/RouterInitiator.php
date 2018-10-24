<?php
namespace Core\Utils;

use App\Provider\AclProvider;
use Core\Collection;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\CollectionInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Router;
use Symfony\Component\Yaml\Yaml;
use Zend\Permissions\Acl\Acl;

final class RouterInitiator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Acl
     */
    private $acl;

    /**
     * @var CollectionInterface
     */
    private $config;

    /**
     * RouterInitiator constructor.
     *
     * @param ContainerInterface $container
     * @param string             $source
     */
    public function __construct(ContainerInterface $container, string $source)
    {
        $this->container = $container;
        $this->config = $this->loadConfig($source);

        $this->checkRouter();
        $this->router = $this->container->get('router');
        $this->acl = $this->container->get(AclProvider::getKey());

        $this->hydrateRouter();
    }

    /***
     * Vérifie que le router soit bien présent dans le Container.
     *
     * @throws \Exception
     */
    public function checkRouter()
    {
        if(!$this->container->has('router')) {
            throw new \Exception("Router not found");
        }
        if(!($router = $this->container->get('router')) instanceof RouterInterface) {
            throw new \Exception("Router must implements '" . RouterInterface::class . "' but '" . get_class($router) . "' given");
        }
    }

    /**
     * Récupère les routes définies et les ajoute au Router.
     */
    private function hydrateRouter()
    {
        $routes = $this->config->get('routes');

        if($this->config->has('middlewares')) {
            // @TODO: Ajouter les middlewares au stack d'exécution de l'application
            throw new \Exception("You must explicitly add your Middlewares to the stack Application and not through the routes declarations file");
        }

        $this->mapRouter($routes);
        // var_dump($this->acl->getRoles(), $this->acl->getResources()); exit;
    }

    /**
     * Parcours les Routes disponiblent et les ajoute au Router.
     *
     * @param  CollectionInterface $routes
     * @throws \Exception
     */
    private function mapRouter(CollectionInterface $routes)
    {
        foreach($routes as $route_name => $params) {
            if($route_name === "middlewares" && is_array($params)) {
                throw new \Exception("You can only declare 'middlewares' under a route or a group. Please check your routes declarations file");
            }

            // liste des paramètres sous forme de Collection
            $params = new Collection($params);

            // on ajoute un groupe
            if(!$params->has('callable') && $params->has('routes')) {
                $group = $this->router->pushGroup(
                    $params->get('pattern'), function () {
                    }
                );
                $group_routes = $this->mapRoutesGroup($params, $route_name);

                // on ajoute les routes du groupe (inception)
                if($group_routes instanceof CollectionInterface) {
                    $this->mapRouter($group_routes);
                }

                // on supprime le groupe pour ne pas affecter les prochaines routes
                $this->router->popGroup();
            }
            elseif (!$params->has('callable')) {
                throw new \Exception("You must at least declare 'callable' for a route or 'routes' for a routes collection in '{$route_name}' declaration");
            }
            // on ajoute une route
            else {
                // la liste des méthodes n'est pas valide...
                if(!$params->has('methods') || !($params->get('methods') instanceof CollectionInterface)) {
                    throw new \Exception("You must declare 'methods' with at least one entry in '{$route_name}' declaration");
                }

                // on filtre les méthodes récupérées pour supprimer celles vides
                $methods = array_filter(
                    $params->get('methods')->all(),
                    function ($value) {
                        return $value !== null; 
                    }
                );

                // au final si on a que des méthodes vides, on averti de nouveau
                if(empty($methods)) {
                    throw new \Exception("You cannot declare empty 'methods' in '{$route_name}' declaration");
                }

                // on ajoute une Route au Router avec les paramètres issus de la configuration utilisateur
                $route = $this->router->map(
                    $methods,
                    $params->get('pattern'),
                    $this->parseCallable($params->get('callable'))
                )->setName($route_name);

                // si la Route a des middlewares qui lui sont attachés, alors on les ajoute
                if($params->has('middlewares') && ($middlewares = $params->get('middlewares')) instanceof CollectionInterface) {
                    $this->addMiddlewares($route, $middlewares, $this->config->get('options.namespaces.middlewares'));
                }

                /*$parents = [];
                if ($params->has('parents')) {
                    $parents = $params->get('parents')->all();
                }

                if ($this->acl->hasResource($route->getName()) === false) {
                    $this->acl->addResource($route->getName());
                }

                // si la Route a des roles qui lui sont attachés, alors on les ajoute
                if($params->has('roles') && ($roles = $params->get('roles')) instanceof CollectionInterface) {
                    foreach($roles as $k => $role) {
                        $mapRole = strtolower(str_replace('ROLE_', '', $role));
                        $roles[$k] = $mapRole;
                    }

                    $route->roles = $roles;
                    $this->acl->allow($roles->all(), $parents);
                }

                $route->parents = $parents;*/
            }
        }
    }

    /**
     * @param string $callable
     * @return string
     */
    private function parseCallable(string $callable): string
    {
        [$class, $method] = explode(':', $callable, 2);

        $class = $this->prependNamespace($class, $this->config->get('options.namespaces.callables'));
        $method = $this->config->get('options.callables.suffix') . $method . $this->config->get('options.callables.prefix');

        return "{$class}:{$method}";
    }

    /**
     * Ajoute le namespace par défaut des Middlewares et les ajoute à la route ou au groupe.
     *
     * @param RouteGroupInterface|RouteInterface $object
     * @param CollectionInterface                $middlewares
     * @param string                             $preprend_namespace
     */
    private function addMiddlewares($object, CollectionInterface $middlewares, ?string $preprend_namespace)
    {
        foreach($middlewares as $middleware) {
            $class = $this->prependNamespace($middleware, $preprend_namespace);
            $object->add($class);
        }
    }

    /**
     * @param CollectionInterface $group
     * @param string $group_name
     *
     * @return null|CollectionInterface
     */
    private function mapRoutesGroup(CollectionInterface $group, string $group_name): ?CollectionInterface
    {
        if ($this->acl->hasResource($group_name) === false) {
            $this->acl->addResource($group_name);
        }

        if($group->has('routes') && count($group->get('routes')) >= 1) {
            /**
             * @var CollectionInterface $routes
             */
            $routes = $group->get('routes');

            foreach ($routes as $k => $route) {
                if (!isset($route['parents'])) {
                    $route['parents'] = [];
                }

                $route['parents'][] = $group_name;
                $routes[$k] = $route;
            }

            $add = function (string $key, CollectionInterface $values) use (&$routes): void {
                // on veut les noms des clés du tableau
                $names = array_keys($routes->all());

                for($i = 0; $i < count($routes); $i++) {
                    $name = $names[$i];

                    /**
                     * @var CollectionInterface $route
                     */
                    $route = $routes->get("'{$name}'");
                    $route[$key] = ($route->has($key)) ? array_merge($route->get($key)->all(), $values->all()) : $values->all();
                    $route[$key] = array_unique($route[$key]->all());

                    $routes[$name] = $route->all();
                }
            };

            // ajout des middlewares du groupe à chaque route enfant
            if($group->has('middlewares') && ($middlewares = $group->get('middlewares')) instanceof CollectionInterface) {
                $add('middlewares', $middlewares);
            }

            // ajout des roles du groupe à chaque route enfant
            if($group->has('roles') && ($roles = $group->get('roles')) instanceof CollectionInterface) {
                $add('roles', $roles);
            }

            // ajout des groupes parents
            if($group->has('parents') && ($parents = $group->get('parents')) instanceof CollectionInterface) {
                $add('parents', $parents);
            }

            return $routes;
        }

        return null;
    }

    /**
     * Ajoute $prepend devant le namespace.
     *
     * @param  $namespace
     * @param  $prepend
     * @return string
     */
    private function prependNamespace(string $namespace, ?string $prepend): string
    {
        if($prepend !== null && substr($namespace, 0, 1) !== '\\') {
            $namespace = "{$prepend}\\{$namespace}";
        }

        return $namespace;
    }

    /**
     * Charge le fichier de configuration des Routes.
     *
     * @param  string $filename
     * @return Collection
     * @throws \Exception
     */
    private function loadConfig(string $filename): Collection
    {
        if(!file_exists($filename)) {
            throw new \Exception("File not found '{$filename}'");
        }

        $yaml = Yaml::parse(file_get_contents($filename));

        return new Collection($yaml);
    }
}
