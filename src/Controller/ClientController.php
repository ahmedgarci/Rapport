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

//      /**
//     * @Route("/Client/GetMySources", name="get_Sources")
//     */
//    public function GetMySources(DataSourceRepository $dbRepository,
//     ClientsRepository $clientsRepository,
//    Request $request,Helpers $helpers,
//    DataSourceRepository $FilesRepository
//    ): JsonResponse
//    {
//        $clientCookie = $request->cookies->get("user");
//        if (!$clientCookie) {
//            return new JsonResponse("unauthorized");
//        }
//        $userId = $helpers->DecodeToken($clientCookie);     
//        $clientEntity = $clientsRepository->find($userId);
//        $dbSources = $dbRepository->findBy(["client"=>$clientEntity]);
//        $filesSource = $FilesRepository->findBy(["client"=>$clientEntity]);
//        return new JsonResponse([$filesSource,$dbSources]);
//    }





}
