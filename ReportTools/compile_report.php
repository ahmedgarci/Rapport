<?php

require '../vendor/autoload.php';

use PHPJasper\PHPJasper;


$input = '../vendor/geekcom/phpjasper/examples/hello_world.jrxml';

$jasper = new PHPJasper;
try {
    $jasper->compile($input)->execute();
    echo "Compilation successful.";
} catch (Exception $e) {
    echo "Error during compilation: " . $e->getMessage();
}

