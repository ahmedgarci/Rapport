<?php

require '../vendor/autoload.php';

use PHPJasper\PHPJasper;

$input = '../vendor/geekcom/phpjasper/examples/hello_world_params.jrxml';
$jasper = new PHPJasper;

try {
    $output = $jasper->listParameters($input)->execute();
    foreach ($output as $parameter_description) {
        print $parameter_description . '<pre>';
    }
} catch (Exception $e) {
    echo "Error listing parameters: " . $e->getMessage();
}
?>
