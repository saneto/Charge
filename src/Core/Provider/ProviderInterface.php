<?php
namespace Core\Provider;

use Pimple\ServiceProviderInterface;

interface ProviderInterface extends ServiceProviderInterface
{
    /**
     * Nom du service (Provider) nécessaire pour l'utiliser par la suite.
     *
     * @return string
     */
    static function getKey(): string;

    /**
     * Fonction à exécuter lors que le provider est appelé via son nom.
     *
     * @return callable
     */
    function getCallable(): callable;

    /**
     * Tableau des paramètres du provider (configuration).
     *
     * @return array
     */
    static function getSettings(): array;
}
