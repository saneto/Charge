<?php
// phpCas 1.3.5 n'est pas compatible avec PHP 7.2+
// jasig/phpcas/source/CAS/Autoload.php : __autoload() is deprecated, use spl_autoload_register()
// (désactivation des erreurs PHP pour les fonctions deprecated).
error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * Dossier racine du projet.
 * @var string
 */
define('ROOT_DIR', dirname(__DIR__));

/**
 * Environnement par défaut de l'applcation.
 */
define('DEFAULT_ENVIRONMENT', 'ENV');

/**
 * Dossier racine des caches.
 * @var string
 */
define('CACHES_DIR', ROOT_DIR . '/cache');

/**
 * Dossier racine des logs.
 * @var string
 */
define('LOGS_DIR', ROOT_DIR . '/logs');

/**
 * Dossier racine de Composer.
 * @var string
 */
define('VENDOR_DIR', ROOT_DIR . '/vendor');

/**
 * Dossier racine des entités Doctrine.
 * @var string
 */
define('DOCTRINE_ENTITY_PATH', ROOT_DIR . '/src/App/Entity/Doctrine');

/**
 * Dossier racine du mapping Doctrine.
 * @var string
 */
define('DOCTRINE_MAPPING_DIR', ROOT_DIR . '/orm/mapping');

/**
 * Dossier racine des migrations Doctrine.
 * @var string
 */
define('DOCTRINE_MIGRATIONS_DIR', ROOT_DIR . '/orm/migrations');
/**
 * Dossier racine des proxies Doctrine.
 * @var string
 */
define('DOCTRINE_PROXIES_DIR', CACHES_DIR . '/orm/proxies');

/**
 * Couleur tk blue.
 * @var string
 */
const TK_RGB_BLUE = "#00A0F5";

/**
 * Couleur tk dark blue.
 * @var string
 */
const TK_RGB_DARK_BLUE = "#003C7D";

/**
 * Couleur tk blue.
 * @var string
 */
const TK_RGB_MEDIUM_BLUE = "#0055BE";

/**
 * Couleur tk green.
 * @var string
 */
const TK_RGB_GREEN = "#9BC832";

/**
 * Couleur tk red.
 * @var string
 */
const TK_RGB_RED = "#FF0050";

/**
 * Couleur tk orange.
 * @var string
 */
const TK_RGB_ORANGE = "#FFB400";

// Inclusion de l'Autoloader de Composer
require_once VENDOR_DIR . '/autoload.php';
?>