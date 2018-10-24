<?php
namespace InterfaceManager;

use Core\Entity\Entity;

/*
 * l'interface qu'on hérité tous les objets manager, cela nous permet de faire du polymophisme
 */
interface IManager
{
    /**
     * @return Entity
     */
    public function getEntity();
    
    /**
     * @param int id
     * @return Entity
     */
    public function getEntityWithID(int $id);
    
    /**
     * @param array $array
     * @return Entity
     */
    public function getEntityWithArray(array  $array);
   
    /**
     * @param Entity $entity
     * @return Entity
     */
    public function getEntityWithEntity(Entity $entity);
    
    
    /**
     * @param Entity $entity
     * @param array  $array
     * @return Entity
     */
    public function getEntityWithEntityAndArray(array  $array, Entity $entity);
    
   
}

