<?php
namespace Core\Entity;

interface EntityInterface extends \JsonSerializable, \ArrayAccess
{
    /**
     * @return string
     */
    function __toString();

    /**
     * Retourne des informations liées à l'entité.
     * Exemple: des données manquantes ou des recommandations.
     *
     * @return array
     */
    function getTips(): array;

    /**
     * Vérifie si l'entité a des tips.
     * @return bool
     */
    function hasTips(): bool;

    /**
     * Ajoute un tip.
     *
     * @param string $type
     * @param string $message
     */
    function addTip(string $type, string $message): void;
}
