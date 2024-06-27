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
                ->setColumn1($requestData["columns"][0])
                ->setColumn2($requestData["columns"][1])
                ->setColumn3($requestData["columns"][2])
                ->setTech($technicien);
            $dbSourceRep->add($rapport, true);
            return new JsonResponse('Demande Envoyée');
        } catch (\Exception $e) {
            return new JsonResponse('Error : ' . $e->getMessage());
        }
    }


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
         $tech = $technicien->find($helpers->DecodeToken($request->cookies->get("user")));
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
            'params' => [
                'NomDuClient' => $DemandInfo['client']['username'],
                'EmailDuClient' => $DemandInfo['client']['email'],
                'NomDuTechnicien' => $tech->getUsername(),
                'inclureField1' => $DemandInfo['Column1']!==null ? 1 : 0,
                'inclureField2' => $DemandInfo['Column2']!==null ? 1 : 0,
                'inclureField3' => $DemandInfo['Column3']!==null ? 1 : 0,
                'field1'=>$DemandInfo["Column1"],
                'field2'=>$DemandInfo["Column2"],                
                'field3'=>$DemandInfo["Column3"],                
                ]
        ];
         
         $jasper = new PHPJasper();
         try {
            $jasper->process($input, $output, $options)->execute();
            $client = $client->findOneBy(["email"=>$DemandInfo["client"]["email"]]);
            $rapport = new Rapports();
            $rapport->setClient($client)
            ->setDate(new \DateTime("now", new \DateTimeZone("Africa/Tunis")))
            ->setReportPath($newFilename)
            ->settitle("Report")->setTech($tech);
            $rap->add($rapport,true);
            $ClientDbsource = $dbSourceRep->findOneBy(["id"=>$DemandInfo["id"]]);
            $ClientDbsource->setIsGenerated(true);
            $em->persist($ClientDbsource);
            $em->flush();
            return new JsonResponse("Generated");
         } catch (\Exception $e) {
             return new JsonResponse('error'. $e->getMessage(), 500);
         }
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