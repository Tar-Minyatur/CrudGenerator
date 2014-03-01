<?php
namespace TSHW\CrudGenerator;

use Analog\Analog;
use Analog\Handler\Stderr;
use TSHW\CrudGenerator\Analyzer\Database\PDOModelAnalyzer;
use TSHW\CrudGenerator\Analyzer\ModelAnalyzer;
use TSHW\CrudGenerator\Model\ModelDescription;
use TSHW\CrudGenerator\Renderer\PDO\PDOEntityRenderer;

class Generator {

    const VERSION = "1.0.0-beta";

    /**
     * @var Config
     */
    private $config;

    public static function handlePHPError ($errno, $errstr, $errfile, $errline ) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    function __construct() {
        set_error_handler(array("\TSHW\CrudGenerator\Generator", "handlePHPError"));
        Analog::handler(Stderr::init());
        return true;
    }


    public function startCLI() {
        if (php_sapi_name() != 'cli') {
            die('
                <p>This script is meant to be run on the commandline!</p>
                <p>Try opening a terminal and typing "php ' . basename($_SERVER['PHP_SELF']) . '".</p>');
        }

        printf("TSHW PHP CRUD Generator - Version %s\n", self::VERSION);
        print "Created by Till Helge Helwig <thh@tshw.de>\n";

        $this->config = new Config();
        // TODO Make the config source more flexible
        $this->config->read("config.json");

        $model = $this->analyzeModel();

        $twigLoader = new \Twig_Loader_Filesystem("templates/");
        $twig = new \Twig_Environment($twigLoader, array(
            'cache' => 'templates/twig_cache/',
            'debug' => true
        ));

        $this->generateEntities($model, $twig);
        $this->generateControllers($model, $twig);
        $this->generateViews($model, $twig);
    }

    private function analyzeModel() {
        Analog::debug("Generator - Analyzing the model");
        $analyzer = new PDOModelAnalyzer();
        // TODO Allow analyzer override via config
        $model = $analyzer->extractModel($this->config);
        return $model;
    }

    private function generateEntities(ModelDescription $model, \Twig_Environment $twig) {
        Analog::debug("Generator - Generating entities");
        // TODO Allow renderer override via config
        $renderer = new PDOEntityRenderer();
        $renderer->renderEntities($model, $twig, $this->config);
    }

    private function generateControllers(ModelDescription $model, \Twig_Environment $twig) {
        Analog::debug("Generator - Generating controllers");
        // TODO Implement
    }

    private function generateViews(ModelDescription $model, \Twig_Environment $twig) {
        Analog::debug("Generator - Generating views");
        // TODO Implement
    }

}