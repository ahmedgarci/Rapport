<?php

namespace App\Controller;

use App\Entity\DBSource;
use PHPJasper\PHPJasper;
use App\Repository\ClientsRepository;
use App\Repository\DBSourceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DBreportsController extends AbstractController
{



    /**
     * @Route("/db/demande", name="show", methods={"POST"})
     */
    public function showDbReport(): Response
    {
        return $this->render('d_breports/index.html.twig');
    }




    /**
     * @Route("/db/demande", name="dbdemande", methods={"POST"})
     */
    public function Save_Report_To_Do(Request $request, DBSourceRepository $dbsource, ClientsRepository $cli): Response
    {
        try {
            $client = $cli->find(23);
            $rapport = new DBSource();
            $rapport->setDriver($request->get('driver'))
                ->setUsername($request->get('username'))
                ->setPassword($request->get('password'))
                ->setHost($request->get('host'))
                ->setDB($request->get('database'))
                ->setClientId($client);
            $dbsource->add($rapport, true);
            
            return new Response('Demande Envoyée');
        } catch (\Exception $e) {
            return new Response('Error : ' . $e->getMessage());
        }
    }






















    /**
     * @Route("/db/showparams", name="dbreport", methods={"POST"})
     */
    public function genererRapport(Request $request): Response
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
        $newFilename = uniqid();
        $output = __DIR__ . '/../../public/reports/' . $newFilename;

        $databaseOptions = [
            'driver' => $request->get("driver"),
            'username' => $request->get("username"),
            'password' => $request->get("password"),
            'host' => $request->get("host"),
            'database' => $request->get("database")
        ];
        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'db_connection' => $databaseOptions,
            'params' => [
                'NomDuClient' => "Saidani Hazem",
                'EmailDuClient' => "saidanihazem022@gmail.com",
                'NomDuTechnicien' => "Garci Ahmed"
            ]
        ];
        $jasper = new PHPJasper;
        try {
            $jasper->process($input, $output, $options)->execute();
            return new Response(
                'Report generated successfully. <a href="/reports/' . $newFilename . '.pdf">Download Report</a>'
            );
        } catch (\Exception $e) {
            return new Response('Error generating report: ' . $e->getMessage());
        }
    }



    /**
     * @Route("/db/showparams", name="showparams")
     */
    public function show_DB_Params(DBSourceRepository $dbsource)
    {
        $params = $dbsource->findAll();
        return $this->render('d_breports/listParams.html.twig', [
            'params' => $params
        ]);
    }









































    /**
     * @Route("/db/csvreport", name="csvReport", methods={"POST"})
     */
    public function generatecsvReport()
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $csvdata = getCsvData();
        $csvJson = json_encode($csvdata);

        $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
        $newFilename = uniqid();
        $output = __DIR__ . '/../../public/reports/' . $newFilename;

        $csvdataJson = json_encode($csvdata);

        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => [
                'NomDuClient' => "Saidani Hazem",
                'EmailDuClient' => "SaidaniHazem022@gmail.com",
                'NomDuTechnicien' => "Garci Ahmed",
                'csvData' => $csvdataJson
            ]
        ];

        $jasper = new PHPJasper;

        try {
            echo "Starting report generation process...\n";
            $jasper->process($input, $output, $options)->execute();
            echo "Report generated successfully.\n";
        } catch (\Exception $e) {
            echo "Error generating report: " . $e->getMessage() . "\n";
        }
    }



    private function getCsvData(): array
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

}