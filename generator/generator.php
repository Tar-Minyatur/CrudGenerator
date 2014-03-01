<?php
require('../vendor/autoload.php');

use TSHW\CrudGenerator;

$generator = new CrudGenerator\Generator();
$generator->startCLI();