<?php
namespace TSHW\CrudGenerator;

use Analog\Analog;
use Analog\Handler\Stderr;
use TSHW\CrudGenerator\Analyzer\Database\PDOModelAnalyzer;
use TSHW\CrudGenerator\Analyzer\ModelAnalyzer;

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

        $this->generateEntities($model);
        $this->generateControllers($model);
        $this->generateViews($model);
    }

    private function analyzeModel() {
        Analog::debug("Generator - Analyzing the model");
        $analyzer = new PDOModelAnalyzer();
        // TODO Allow analyzer override via config
        $model = $analyzer->extractModel($this->config);
        return $model;
    }

    private function generateEntities($model) {
        Analog::debug("Generator - Generating entities");
        // TODO Implement
    }

    private function generateControllers($model) {
        Analog::debug("Generator - Generating controllers");
        // TODO Implement
    }

    private function generateViews($model) {
        Analog::debug("Generator - Generating views");
        // TODO Implement
    }

}