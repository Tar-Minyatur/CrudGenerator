<?php
namespace TSHW\CrudGenerator;

use Analog\Analog;
use Analog\Handler\File;
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
        if (!file_exists("logs")) {
            mkdir("logs");
        }
        Analog::handler(File::init(sprintf("logs/generate-%s.log", date('Y-m-d_H-i'))));
        return true;
    }


    public function startCLI() {
        if (php_sapi_name() != 'cli') {
            die('
                <p>This script is meant to be run on the commandline!</p>
                <p>Try opening a terminal and typing "php ' . basename($_SERVER['PHP_SELF']) . '".</p>');
        }

        printf("TSHW PHP CRUD Generator - Version %s\n", self::VERSION);
        print "Created by Till Helge Helwig <thh@tshw.de>\n\n";

        $this->config = new Config();
        // TODO Make the config source more flexible
        $this->config->read("config.json");

        $this->generateCode();
    }

    public function generateCode() {
        $startTime = microtime();

        $model = $this->analyzeModel();

        $twigLoader = new \Twig_Loader_Filesystem("templates/");
        $twig = new \Twig_Environment($twigLoader, array(
            'cache' => 'templates/twig_cache/',
            'debug' => true
        ));

        $this->setupApplication($twig);
        $this->generateEntities($model, $twig);
        $this->generateControllers($model, $twig);
        $this->generateViews($model, $twig);

        $durationInSeconds = round((microtime() - $startTime / 1000), 2);
        echo "\nFinished code generation after " . $durationInSeconds . "s\n";
        echo "Application has been created in " . $this->config->appBaseDir . $this->config->appDir . "\n\n";
        echo "Running Composer...\n";
        system('cd ' . $this->config->appBaseDir . $this->config->appDir . ' && composer install');
    }

    private function setupApplication(\Twig_Environment $twig) {
        if (!file_exists($this->config->appBaseDir)) {
            Analog::debug("Creating application directory: " . $this->config->appBaseDir);
            mkdir($this->config->appBaseDir, 0777, true);
        }

        $fileName = $this->config->appBaseDir . $this->config->appDir . '/composer.json';
        $variables = array(
            'namespace' => $this->config->appNamespace
        );

        if (!file_exists($fileName)) {
            Analog::debug("Generator - Creating composer.json");
            $template = $twig->loadTemplate("json/composer.twig");
            file_put_contents($fileName, $template->render($variables));
        } else {
            Analog::debug("Generator - Skipping composer.json because it already exists");
        }
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