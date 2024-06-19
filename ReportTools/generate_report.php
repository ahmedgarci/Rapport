<?php

require '../vendor/autoload.php';

use PHPJasper\PHPJasper;

$input = '../vendor/geekcom/phpjasper/examples/hello_world.jasper';
$output = '../vendor/geekcom/phpjasper/examples';
$options = [
    'format' => ['pdf', 'rtf']
];



$jasper = new PHPJasper;
try {
    echo "Starting report generation process...\n";
    $jasper->process($input, $output, $options)->execute();
    echo "Report generated successfully.\n";
} catch (Exception $e) {
    echo "Error generating report: " . $e->getMessage() . "\n";
}