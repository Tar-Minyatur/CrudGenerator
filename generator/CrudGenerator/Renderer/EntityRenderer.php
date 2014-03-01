<?php
namespace TSHW\CrudGenerator\Renderer;

use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Model\ModelDescription;

interface EntityRenderer {

    function renderEntities(ModelDescription $model, \Twig_Environment $twig, Config $config);

}