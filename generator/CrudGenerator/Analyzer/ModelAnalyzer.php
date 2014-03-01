<?php
namespace TSHW\CrudGenerator\Analyzer;

use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Model\ModelDescription;

interface ModelAnalyzer {

    /**
     * Extract a description of the data model from the designated information source.
     * @param Config $config Configuration object
     * @return ModelDescription
     */
    function extractModel(Config $config);

}