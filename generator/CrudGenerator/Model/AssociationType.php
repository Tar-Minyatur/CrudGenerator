<?php
namespace TSHW\CrudGenerator\Model;

interface AssociationType {

    const ONE_TO_ONE = 1;
    const ONE_TO_MANY = 2;
    const MANY_TO_ONE = 3;
    const MANY_TO_MANY = 4;

}