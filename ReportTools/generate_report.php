<?php

use PHPJasper\PHPJasper;
require '../vendor/autoload.php';






function readTxtFile(): string {
    $filePath = __DIR__ . '/../public/uploads/symfony.txt';

    $fileHandle = fopen($filePath, 'r');
    $fileContents = '';
    while (($line = fgets($fileHandle)) !== false) {
        $fileContents .= $line;
    }

    fclose($fileHandle);

    return $fileContents;
}












$txt = readTxtFile();
var_dump($txt);

$input = __DIR__ . '/../vendor/geekcom/phpjasper/examples/hello_world_TxT.jrxml';
$output = __DIR__ . '/../public/reports/testCSV';

$options = [
    'format' => ['csv'],
    'locale' => 'en',
    'params' => [
        'NomDuClient' => "Saidani Hazem",
        'EmailDuClient' => "SaidaniHazem022@gmail.com",
        'NomDuTechnicien' => "Garci Ahmed",
        'contenueCSV' => $txt
    ]];

$jasper = new PHPJasper;

try {
    echo "Starting report generation process...\n";
    $jasper->process($input, $output, $options)->execute();
    echo "Report generated successfully.\n";
} catch (Exception $e) {
    echo "Error generating report: " . $e->getMessage() . "\n";
}