<?php

use PHPJasper\PHPJasper;
require '../vendor/autoload.php';

$input = __DIR__ . '/../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
$newFilename = uniqid();

$output = __DIR__ . '/../public/reports/' . $newFilename;

$databaseOptions = [
    'driver' => 'mysql',
    'username' => 'root',
    'password' => '0000', 
    'host' => '127.0.0.1',
    'database' => 'rapport',
];


$options = [
    'format' => ['pdf'],
    'locale' => 'en',
    'db_connection' => $databaseOptions
   
];

$jasper = new PHPJasper;
try {
    echo "Starting report generation process...\n";
    $jasper->process($input, $output, $options)->execute();
    echo "Report generated successfully.\n";
} catch (Exception $e) {
    echo "Error generating report: " . $e->getMessage() . "\n";
}