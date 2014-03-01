<?php
namespace TSHW\CrudGenerator\Renderer\PDO;

use Analog\Analog;
use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Model\ModelDescription;
use TSHW\CrudGenerator\Renderer\EntityRenderer;

class PDOEntityRenderer implements EntityRenderer {

    function renderEntities(ModelDescription $model, \Twig_Environment $twig, Config $config) {
        echo "Creating entity objects...\n";

        $modelDir = $config->appBaseDir . "/Model/Generated/";
        $namespace = $config->appNamespace . "\\Model\\Generated";

        if (!file_exists(dirname($modelDir))) {
            Analog::debug("Creating directory for entities: " . $modelDir);
            mkdir($modelDir, 0777, true);
        }

        foreach ($model->getEntities() as $entity) {
            $fileName = $modelDir . $entity->getName() . ".php";
            $variables = array(
                'namespace' => $namespace,
                'entity' => $entity
            );

            Analog::debug("PDOEntityRenderer - Writing entity '" . $entity->getName() . "' to file: " . $fileName);
            $template = $twig->loadTemplate("php/entity.twig");
            file_put_contents($fileName, $template->render($variables));
            echo "Created entity '" . $entity->getName() . "'\n";
        }

    }
}