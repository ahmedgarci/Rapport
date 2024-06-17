<?php

function getCsvData(): array
{
    $csvFilePath = '../public/uploads/0eb6eb19ddead5b87a9e25ec4c5a0ecf.csv';
    $file = fopen($csvFilePath, 'r');
    $csvData = [];
    while (($row = fgetcsv($file)) !== false) {
        $csvData[] = $row;
    }
    fclose($file);
    return $csvData;
}

$csvdata = getCsvData();

use PHPJasper\PHPJasper;
require '../vendor/autoload.php';


$input = __DIR__ . '/../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
$output = __DIR__ . '/../public/reports/output';
$csvFilePath = __DIR__ . '/../public/0eb6eb19ddead5b87a9e25ec4c5a0ecf.csv';

$options = [
    'format' => ['pdf'],
    'locale' => 'en',
    'params' => [
        'NomDuClient' => "Saidani Hazem",
        'EmailDuClient' => "SaidaniHazem022@gmail.com",
        'NomDuTechnicien' => "Garci Ahmed"
    ]
];

$jasper = new PHPJasper;
try {
    echo "Starting report generation process...\n";
    $jasper->process($input, $output, $options)->execute();
    echo "Report generated successfully.\n";
} catch (Exception $e) {
    echo "Error generating report: " . $e->getMessage() . "\n";
}

