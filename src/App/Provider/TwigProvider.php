<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class TwigProvider extends ProviderFactory
{
    /**
     * @return array
     */
    public static function getSettings(): array
    {
        $env = AppProvider::getSettings()['env'];

        return [
            'debug' => ($env === 'dev'),
            'cache' => static::getCacheDir($env),
            'templates.path' => static::getTemplatesPath()
        ];
    }

    /**
     * Créer une instance de Twig pour la gestion des vues.
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (ContainerInterface $container): Twig {
            $settings = static::getSettings();
            $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/') . '/';

            $view = new Twig(
                $settings['templates.path'], [
                'cache' => $settings['cache']
                ]
            );
            $view->addExtension(new TwigExtension($container['router'], $basePath));
            $view->addExtension(new \Twig_Extension_Debug());

            // on active la fonction "dump()" de Twig pour le mode debug
            if($settings['debug'] === true) {
                $view->getEnvironment()->enableDebug();
            }

            return $view;
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'view';
    }

    /**
     * @param string $env
     * @return false|string
     */
    public static function getCacheDir(string $env)
    {
        switch ($env) {
            case 'dev':
                return false;
            default:
                return CACHES_DIR . '/twig/views';
        }
    }

    /**
     * @return string
     */
    public static function getTemplatesPath(): string
    {
        return ROOT_DIR . "/views";
    }
}
