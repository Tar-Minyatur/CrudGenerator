<?php
namespace TSHW\CrudGenerator\Renderer\PDO;

use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Model\ModelDescription;
use TSHW\CrudGenerator\Renderer\EntityRenderer;

class PDOEntityRenderer implements EntityRenderer {

    function renderEntities(ModelDescription $model, \Twig_Environment $twig, Config $config) {

        foreach ($model->getEntities() as $entity) {
            $fileName = $config->appBaseDir . "/Model/Generated/" . $entity->getName() . ".php";
            $namespace = $config->appNamespace . "\\Model\\Generated";
            $variables = array(
                'namespace' => $namespace,
                'entity' => $entity
            );

            if (!file_exists(dirname($fileName))) {
                mkdir(dirname($fileName), 0777, true);
            }
            $template = $twig->loadTemplate("php/entity.twig");
            file_put_contents($fileName, $template->render($variables));
        }

    }
}