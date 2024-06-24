<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\DBSource;
use App\Entity\Rapports;
use App\Repository\RapportsRepository;
use App\Repository\TechnicienRepository;
use PHPJasper\PHPJasper;
use App\Repository\ClientsRepository;
use App\Repository\DBSourceRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;



class DBreportsController extends AbstractController
{
    /**
     * @Route("/Client/DemandeRapport", name="dbdemande", methods={"POST"})
     */
    public function Save_Report_To_Do(Request $request,JWTEncoderInterface $jwtEncoder,
    TechnicienRepository $techRepo,SerializerInterface $serializer,
    DBSourceRepository $dbSourceRep, ClientsRepository $clientRep): Response
    {
  //      $token = $request->cookies->get("user");
  //      if(!$token){
  //          return new JsonResponse('Unauthorized',Response::HTTP_UNAUTHORIZED);
  //      }
        $technicien = $techRepo->findOneBy(["id"=> 1]);
 //       if(!$technicien){
  //          return new JsonResponse('Technicien n existe pas !');
  //      }
        $requestData = json_decode($request->getContent(),true);
  //     $userData = $jwtEncoder->decode($token);
        try {
            $client = $clientRep->findOneBy(["id"=>1]);
            $rapport = new DBSource();
            $rapport->setDriver($requestData["driver"])
                ->setUsername($requestData["username"])
                ->setPassword($requestData["password"])
                ->setHost($requestData["host"])
                ->setDB($requestData["driver"])
                ->setClientId($client)
                ->setTech($technicien);
            $dbSourceRep->add($rapport, true);
            return new Response('Demande Envoyée');
        } catch (\Exception $e) {
            return new Response('Error : ' . $e->getMessage());
        }}



    /**
     * @Route("/db/showparams", name="dbreport", methods={"POST"})
     */
    public function genererRapport(Request $request, ClientsRepository $client , RapportsRepository $rap, TechnicienRepository $technicien): Response
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

        $cli = $client->find(23);
        $techni = $technicien->find(2);

        $rapport = new Rapports();
        $rapport->setClient($cli)->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))->settitle("Report")->setTech($techni)->setReportPath($newFilename);
        $rap->add($rapport);
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
     * @Route("/db/csvreport", name="csvReport")
     */
    public function generatecsvReport(ClientsRepository $client , RapportsRepository $rap, TechnicienRepository $technicien): Response
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_worldTocsv.jrxml';
        $newFilename = uniqid();
        $output = __DIR__ . '/../../public/reports/' . $newFilename;
       
        $data = getCsvData();
        $csvString = csvToString($data);


        $options = [
            'format' => ['pdf'],
            'locale' => 'en',
            'params' => [
                'NomDuClient' => "Saidani Hazem",
                'EmailDuClient' => "SaidaniHazem022@gmail.com",
                'NomDuTechnicien' => "Garci Ahmed",
                'contenueCSV' => $csvString
            ]];
        $jasper = new PHPJasper;
        
        try {
            echo "Starting report generation process...\n";
            $jasper->process($input, $output, $options)->execute();

            $cli = $client->find(23);
            $techni = $technicien->find(2);

            $rapport = new Rapports();
            $rapport->setClient($cli)->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))->settitle("Report")->setTech($techni)->setReportPath($newFilename);
            $rap->add($rapport);

            return new Response("bien genere");
        } catch (\Exception $e) {
            return new Response("eurreur :  ". $e->getMessage()); 
        }}




        /**
         * @Route("/db/txtreport", name="txtReport")
         */
        public function txtreports(ClientsRepository $client , RapportsRepository $rap, TechnicienRepository $technicien): Response
        {
            require __DIR__ . '/../../vendor/autoload.php';
            $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world_TxT.jrxml';
            $newFilename = uniqid();
            $output = __DIR__ . '/../../public/reports/' . $newFilename;

            $txt = readTxtFile();

            $options = [
                'format' => ['pdf'],
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
                $rapport = new Rapports();
                $cli = $client->find(23);
            $techni = $technicien->find(2);
                $rapport->setClient($cli)->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))->settitle("Report")->setTech($techni)->setReportPath($newFilename);
                $rap->add($rapport);

                return new Response("generer");

        } catch (\Exception $e) {
            return new Response("euureur :  ". $e->getMessage()); 
        }}}
    
    
    
    function getCsvData(): array{
        $csvFilePath = '../public/uploads/Population.csv';
        $file = fopen($csvFilePath, 'r');
        $csvData = [];
        $headers = fgetcsv($file);
        while (($row = fgetcsv($file)) !== false) {
            $csvData[] = array_combine($headers, $row);}
        fclose($file);
        return $csvData;
    }

    function csvToString(array $data): string {
        $csvString = '';
        foreach ($data as $row) {
            $csvString .= implode('     ', $row);}
        return $csvString;
    }

    function readTxtFile(): string {
        $filePath ='../public/uploads/symfony.txt';
    
        $fileHandle = fopen($filePath, 'r');
        $fileContents = '';
        while (($line = fgets($fileHandle)) !== false) {
            $fileContents .= $line;
        }
        fclose($fileHandle);
        return $fileContents;
    }