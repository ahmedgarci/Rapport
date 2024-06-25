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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
use Doctrine\ORM\EntityManagerInterface;



class DBreportsController extends AbstractController
{
    

    /**
     * @Route("/Client/DemandeRapport", name="dbdemande", methods={"POST"})
     */
    public function Save_Report_To_Do(Request $request,JWTEncoderInterface $jwtEncoder,
    TechnicienRepository $techRepo,SerializerInterface $serializer,Helpers $helpers,
    DBSourceRepository $dbSourceRep, ClientsRepository $clientRep): JsonResponse
    {
        $token = $request->cookies->get("user");
        if(!$token){
            return new JsonResponse('Unauthorized',Response::HTTP_UNAUTHORIZED);            
        }
        $requestData = json_decode($request->getContent(),true);
        $technicien = $techRepo->findOneBy(["email"=> $requestData["emailTech"]]);
        if(!$technicien){
            return new JsonResponse('Technicien n existe pas !');            
        }
        $ClientId = $helpers->DecodeToken($token);
        try {
            $client = $clientRep->findOneBy(["id"=>$ClientId]);
            $rapport = new DBSource();
            $rapport->setDriver($requestData["driver"])
                ->setUsername($requestData["username"])
                ->setPassword($requestData["password"])
                ->setHost($requestData["host"])
                ->setDB($requestData["driver"])
                ->setClient($client)
                ->setTech($technicien);
            $dbSourceRep->add($rapport, true);
            return new JsonResponse('Demande Envoyée');
        } catch (\Exception $e) {
            return new Response('Error : ' . $e->getMessage());
        }}



    /**
     * @Route("/Techniciens/GenererRapportAvecDB", name="dbreport", methods={"POST"})
     */
     public function genererRapport(Request $request, Helpers $helpers,
     DBSourceRepository $dbSourceRep,EntityManagerInterface $em,
     ClientsRepository $client, RapportsRepository $rap, 
     TechnicienRepository $technicien): JsonResponse
     {
         require __DIR__ . '/../../vendor/autoload.php';
         $input = __DIR__ . '/../../vendor/geekcom/phpjasper/examples/hello_world.jrxml';
         $newFilename = uniqid();
         $output = __DIR__ . '/../../public/reports/' . $newFilename;
        
 
         $DemandInfo = json_decode($request->getContent(), true)["DBInfo"];
         $databaseOptions = [
             'driver' => $DemandInfo["driver"],
             'username' => $DemandInfo["username"],
             'password' => $DemandInfo["password"],
             'host' => $DemandInfo["host"],
             'database' => $DemandInfo["DB"]
         ];
 
         $options = [
             'format' => ['pdf'],
             'locale' => 'en',
             'db_connection' => $databaseOptions,
         ];
         $jasper = new PHPJasper();
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
            $client = $client->findOneBy(["email"=>$DemandInfo["client"]["email"]]);
            $tech = $technicien->find($helpers->DecodeToken($request->cookies->get("user")));
            $rapport = new Rapports();
            $rapport->setClient($client)
            ->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))
            ->setReportPath($newFilename)
            ->settitle("Report")->setTech($tech);
            $rap->add($rapport,true);
            $ClientDbsource = $dbSourceRep->find(["id"=>$DemandInfo["id"]]);
            $ClientDbsource->setIsGenerated(true);
            $em->persist($ClientDbsource);
            $em->flush();
            return new JsonResponse("Generated");
         } catch (\Exception $e) {
             return new JsonResponse('error'. $e->getMessage(), 500);
         }
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