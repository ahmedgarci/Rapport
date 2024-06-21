<?php
use PHPJasper\PHPJasper;
require '../vendor/autoload.php';



function getCsvData(): array
{
    $csvFilePath = '../public/uploads/0eb6eb19ddead5b87a9e25ec4c5a0ecf.csv';
    $file = fopen($csvFilePath, 'r');
    $csvData = [];
    $headers = fgetcsv($file);
    while (($row = fgetcsv($file)) !== false) {
        $csvData[] = array_combine($headers, $row);
    }
    fclose($file);
    return $csvData;
}


$csvdata = getCsvData();



$input = __DIR__ . '/../vendor/geekcom/phpjasper/examples/hello_worldTocsv.jrxml';
$newFilename = uniqid();
$output = __DIR__ . '/../public/reports/testCSV';


$options = [
    'format' => ['pdf'],
    'locale' => 'en',
    'params' => [
        'NomDuClient' => "Saidani Hazem",
        'EmailDuClient' => "SaidaniHazem022@gmail.com",
        'NomDuTechnicien' => "Garci Ahmed"]];
$jasper = new PHPJasper;
try {
    echo "Starting report generation process...\n";
    $jasper->process($input, $output, $options)->execute();
    echo "Report generated successfully.\n";
} catch (Exception $e) {
    echo "Error generating report: " . $e->getMessage() . "\n";
}