<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RapportsRepository;
use App\Repository\ClientsRepository;
use App\Repository\DBSourceRepository;
use App\Repository\DataSourceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use PDO;
use App\Helpers;

class ClientController extends AbstractController
{
    /**
     * @Route("/Client/GetMyReports", name="app_client")
     */
    public function index(RapportsRepository $rapportsRepository, ClientsRepository $clientsRepository,
    Request $request,Helpers $helpers
    ): JsonResponse
    {
        $clientCookie = $request->cookies->get("user");
        if (!$clientCookie) {
            return new JsonResponse("unauthorized");
        }
        $userId = $helpers->DecodeToken($clientCookie);     
        $clientEntity = $clientsRepository->find($userId);
        $reports = $rapportsRepository->findBy(["client"=>$clientEntity]);
        $serializedReports = [];
        foreach ($reports as $report) {
            $serializedReports[] = [
                'id' => $report->getId(),
                'title' => $report->getTitle(),
                'Repport_Path' =>$report->getReportPath(),
                'Date'=>$report->getdate(),
                'Publisher'=>$report->getTech()
            ];
        }        
        return new JsonResponse($serializedReports);
    }
    /**
     * @Route("/Client/getTables", name="GetMyTables", methods={"POST"})
     */
     public function getTables(
        Request $request 
    ){
        $DemandInfo = json_decode($request->getContent(), true);
        $servername = $DemandInfo['host'];
        $username = $DemandInfo["username"];
        $password = $DemandInfo["password"];
        $dbname = $DemandInfo["DB"];
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SHOW TABLES");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return new JsonResponse($result);
        } catch(PDOException $e) {
        return new JsonResponse($e->getMessage());
        }          
    }

    /**
     * @Route("/Client/getColNames", name="GetColNames", methods={"POST"})
     */
    public function getColNames(
        Request $request 
    ){
        $DemandInfo = json_decode($request->getContent(), true);
        $servername = $DemandInfo['host'];
        $username = $DemandInfo["username"];
        $password = $DemandInfo["password"];
        $dbname = $DemandInfo["DB"];
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $requette = "describe ". $DemandInfo["table"];
            $stmt = $conn->prepare($requette);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return new JsonResponse($result);
        } catch(PDOException $e) {
        return new JsonResponse($e->getMessage());
        }          
    }





}
