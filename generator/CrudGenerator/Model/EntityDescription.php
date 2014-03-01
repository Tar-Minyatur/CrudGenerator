<?php
namespace TSHW\CrudGenerator\Model;

use TSHW\CrudGenerator\Exception\AnalysisException;

class EntityDescription {

    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $tableName;

    /**
     * @var EntityFieldDescription[] fieldName => Field
     */
    private $fields = array();

    /**
     * @var EntityAssociationDescription[] mappingField => Association
     */
    private $associations = array();

    /**
     * @var EntityFieldDescription
     */
    private $primaryKey;

    /**
     * @param String $name
     * @param String $tableName
     * @param EntityFieldDescription[] $fields
     * @param EntityAssociationDescription[] $associations
     */
    function __construct($name, $tableName, $fields = array(), $associations = array()) {
        $this->name = $name;
        $this->tableName = $tableName;
        $this->fields = $fields;
        $this->associations = $associations;
    }

    public function addField(EntityFieldDescription $field) {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @return EntityFieldDescription[]
     */
    public function getFields() {
        return $this->fields;
    }

    public function addAssociation(EntityAssociationDescription $association) {
        if ($association->getMappingField() != null) {
            $fieldName = $association->getMappingField()->getName();
        } else {
            $fieldName = '_' . $association->getTargetEntity()->getName();
        }
        $this->associations[$fieldName] = $association;
    }

    /**
     * @return EntityAssociationDescription[]
     */
    public function getAssociations() {
        return $this->associations;
    }

    /**
     * @return String
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return String
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @param EntityFieldDescription $primaryKey
     */
    public function setPrimaryKey($primaryKey) {
        if (!in_array($primaryKey, $this->fields)) {
            throw new AnalysisException("Primary key has to be one of the entity's fields");
        }
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return EntityFieldDescription
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }



}