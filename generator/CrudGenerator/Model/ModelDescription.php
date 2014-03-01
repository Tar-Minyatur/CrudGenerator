<?php
namespace TSHW\CrudGenerator\Model;

class ModelDescription {

    /**
     * @var EntityDescription[]
     */
    private $entities = array();

    public function addEntity(EntityDescription $entity) {
        $this->entities[] = $entity;
    }

    /**
     * @return EntityDescription[]
     */
    public function getEntities() {
        return $this->entities;
    }



}