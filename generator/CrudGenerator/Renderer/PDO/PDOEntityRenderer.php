<?php
namespace TSHW\CrudGenerator\Renderer\PDO;

use Analog\Analog;
use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Model\ModelDescription;
use TSHW\CrudGenerator\Renderer\EntityRenderer;

class PDOEntityRenderer implements EntityRenderer {

    function renderEntities(ModelDescription $model, \Twig_Environment $twig, Config $config) {
        echo "Creating entity objects...\n";

        $modelDir = $config->appBaseDir . $config->appDir . "/Model/";
        $generatedModelDir = $modelDir . "Generated/";
        $namespace = $config->appNamespace . "\\Model";
        $generatedNamespace = $namespace . "\\Generated";

        if (!file_exists($generatedModelDir)) {
            Analog::debug("Creating directory for entities: " . $generatedModelDir);
            mkdir($generatedModelDir, 0777, true);
        }

        foreach ($model->getEntities() as $entity) {
            $variables = array(
                'namespace' => $generatedNamespace,
                'entity' => $entity
            );
            $fileName = $generatedModelDir . $entity->getName() . ".php";

            Analog::debug("PDOEntityRenderer - Writing entity '" . $entity->getName() . "' to file: " . $fileName);
            $template = $twig->loadTemplate("php/entity.twig");
            file_put_contents($fileName, $template->render($variables));

            $variables = array(
                'namespace' => $namespace,
                'entity' => $entity
            );
            $fileName = $modelDir . $entity->getName() . ".php";

            Analog::debug("PDOEntityRenderer - Writing entity override object '" . $entity->getName() . "' to file: " . $fileName);
            $template = $twig->loadTemplate("php/entity_stub.twig");
            file_put_contents($fileName, $template->render($variables));
            echo "Created entity '" . $entity->getName() . "'\n";
        }

    }
}