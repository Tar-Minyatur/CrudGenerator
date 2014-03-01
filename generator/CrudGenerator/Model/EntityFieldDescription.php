<?php
namespace TSHW\CrudGenerator\Model;

class EntityFieldDescription {

    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $type;

    /**
     * @var boolean
     */
    private $editable;

    /**
     * @var boolean
     */
    private $mandatory;

    function __construct($name, $type, $mandatory = false, $editable = true) {
        $this->name = $name;
        $this->type = $type;
        $this->mandatory = $mandatory;
        $this->editable = true;
    }

    /**
     * @return boolean
     */
    public function getEditable() {
        return $this->editable;
    }

    /**
     * @param boolean $editable
     */
    public function setEditable($editable) {
        $this->editable = $editable;
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
    public function getType() {
        return $this->type;
    }

    /**
     * @param boolean $mandatory
     */
    public function setMandatory($mandatory) {
        $this->mandatory = $mandatory;
    }

    /**
     * @return boolean
     */
    public function getMandatory() {
        return $this->mandatory;
    }

}