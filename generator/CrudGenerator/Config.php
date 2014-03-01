<?php
namespace TSHW\CrudGenerator;

use Analog\Analog;
use TSHW\CrudGenerator\Exception\ConfigurationException;

class Config {

    /**
     * @var array Settings stored as key => value pairs
     */
    private $settings;

    public function __get($name) {
        if (isset($this->settings[$name])) {
            return $this->settings[$name];
        } else {
            return null;
        }
    }

    public function __set($name, $value) {
        $this->settings[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->settings[$name]);
    }

    public function read($filePath) {
        try {
            Analog::debug("Config - Reading config from file: " . $filePath);
            $json = json_decode(file_get_contents($filePath), true);
            Analog::debug("Config - Read " . sizeof($json) . " configuration setting(s)");
            $this->settings = $json;
        }
        catch (\Exception $ex) {
            Analog::error("Config - Configuration could not be read. Exception: " . $ex->getMessage());
            throw new ConfigurationException("Configuration could not be read", 0, $ex);
        }
    }

}