<?php
namespace TSHW\CrudGenerator\Model;

class EntityAssociationDescription {

    /**
     * @var AssociationType.CONSTANT
     */
    private $type;

    /**
     * @var EntityDescription
     */
    private $targetEntity;

    /**
     * @var EntityFieldDescription
     */
    private $mappingField;

    /**
     * @var String
     */
    private $mappingTable;

    /**
     * @param AssociationType.CONSTANT $type
     * @param EntityDescription $targetEntity
     * @param EntityFieldDescription $mappingField
     */
    function __construct($type, EntityDescription $targetEntity, EntityFieldDescription $mappingField = null) {
        $this->type = $type;
        $this->targetEntity = $targetEntity;
        $this->mappingField = $mappingField;
        $this->mappingTable = null;
    }

    /**
     * @return EntityFieldDescription
     */
    public function getMappingField() {
        return $this->mappingField;
    }

    /**
     * @return EntityDescription
     */
    public function getTargetEntity() {
        return $this->targetEntity;
    }

    /**
     * @return AssociationType.CONSTANT
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param AssociationType.CONSTANT $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @param String $mappingTable
     */
    public function setMappingTable($mappingTable) {
        $this->mappingTable = $mappingTable;
    }

    /**
     * @return String
     */
    public function getMappingTable() {
        return $this->mappingTable;
    }



}