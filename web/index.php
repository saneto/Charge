<?php
use App\Provider;

/**
 * @TODO: Placer ailleurs la fonction ?
 *
 * @see http://woocommerce.wp-a2z.org/oik_api/wc_light_or_dark/
 *
 * @param string $color
 * @param string $dark
 * @param string $light
 *
 * @return string
 */
function reverse_color(string $color, string $dark = '#000000', string $light = '#FFFFFF')
{
    $hex = str_replace('#', '', $color);
    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    $brightness = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;

    return $brightness > 185 ? $dark : $light;
}

// Configuration de l'application + autoload Composer
require dirname(__DIR__) . '/config/config.php';

/* -------------------------------------------------------*/

/**
 * Préparation du Container pour Slim Framework.
 * @var \Slim\Container $container
 */
$container = (new \Slim\Container)
    ->register(new Provider\AppProvider)
    ->register(new Provider\AclProvider)
    ->register(new Provider\CasProvider)
    ->register(new Provider\AuthProvider)
    ->register(new Provider\TwigProvider)
    ->register(new Provider\DoctrineProvider)
    ->register(new Provider\ValidatorProvider)
    ->register(new Provider\FlashMessagesProvider)
    ->register(new \Core\Provider\FoundProvider)
    ->register(new \Core\Provider\NotFoundProvider);

/* -------------------------------------------------------*/

// Ajout des extensions et variables liées à l'application
$container->extend('view', function (\Slim\Views\Twig $view, \Psr\Container\ContainerInterface $container): \Slim\Views\Twig
{
    $env = $view->getEnvironment();

    // Variables globales
    $env->addGlobal('menu_item', 'accueil');
    $env->addGlobal('app', $container->get('app'));
    $env->addGlobal('user', ($container->auth->hasIdentity()) ? $container->auth->getIdentity() : []);

    // Extensions
    $env->addExtension(new \App\Twig\TwigExtensions);

    return $view;
});

/* -------------------------------------------------------*/

// Hydratation du Router de Slim Framework en utilisant le fichier YAML des routes
$routerInitiator = new \Core\Utils\RouterInitiator($container, ROOT_DIR . '/routes.yml');

// Démarrage de Slim Framework avec le Container prêt à être utilisé
$Slim = (new \Slim\App($container))
    ->add(Provider\AuthProvider::getAuthMiddleware());
//    ->add(Provider\AclProvider::getAclMiddleware());

$Slim->run();
