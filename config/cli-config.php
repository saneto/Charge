<?php

use App\Provider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\Setup;

$isDevMode = getenv('ENV') === 'prod';
$settings = Provider\DoctrineProvider::getSettings();

$driver = new SimplifiedYamlDriver($settings['meta']['entity_namespaces']);

$config = Setup::createYAMLMetadataConfiguration($settings['meta']['entity_paths'], $isDevMode);
$config->setMetadataDriverImpl($driver);

$entityManager = EntityManager::create($settings['connection'], $config);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
?>