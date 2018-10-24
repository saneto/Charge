<?php
namespace App\Provider;

use Core\Provider\ProviderFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;

class DoctrineProvider extends ProviderFactory
{
    /**
     * @return array
     */
    public static function getSettings(): array
    {
        $env = AppProvider::getSettings()['env'];

        return [
            'connection' => static::getConnectionSettings($env),
            'meta' => [
                'auto_generate_proxies' => true,
                'proxy_dir' => CACHES_DIR . '/orm/proxies',
                'entity_paths' => [
                    ROOT_DIR . '/src/Entity',
                    ROOT_DIR . '/src/Entity/Superclass',
                    ROOT_DIR . '/src/Entity/Dev'
                ],
                'entity_namespaces' => [
                    ROOT_DIR . '/orm/mapping'            => "App\\Entity\\Doctrine",
                    ROOT_DIR . '/orm/mapping/superclass' => "App\\Entity\\Doctrine\\Superclass",
                    ROOT_DIR . '/orm/mapping/dev'        => "App\\Entity\\Dev"
                ]
            ]
        ];
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (ContainerInterface $container): EntityManager {
            $isDevMode = (getenv('ENV') === 'dev');
            $settings = static::getSettings();

            $driver = new SimplifiedYamlDriver($settings['meta']['entity_namespaces']);

            $config = Setup::createYAMLMetadataConfiguration($settings['meta']['entity_paths'], $isDevMode);
            $config->setMetadataDriverImpl($driver);
            $config->setAutoGenerateProxyClasses($settings['meta']['auto_generate_proxies'] ?? null);
            $config->setProxyDir($settings['meta']['proxy_dir'] ?? null);

            return EntityManager::create($settings['connection'], $config);
        };
    }

    /**
     * @return string
     */
    public static function getKey(): string
    {
        return 'doctrine';
    }

    /**
     * @param string $env
     * @return array
     */
    public static function getConnectionSettings(string $env)
    {
        $defaults = [
            'driver'  => 'pdo_mysql',
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_unicode_ci',
        ];

        $settings = [
            'host'     => 'localhost',
            'user'     => 'root',
            'password' => ''
        ];

        switch ($env) {
            case 'dev':
                $settings['dbname'] = 'charge';
                $settings['port'] = 3306;
                break;
            case 'tests':
                $settings['dbname'] = 'charge_cde_tests';
                $settings['port'] = 3316;
                break;
            case 'prod':
                $settings['dbname'] = 'charge_cde';
                $settings['port'] = 3316;
                break;
        }

        return array_merge($defaults, $settings);
    }
}
