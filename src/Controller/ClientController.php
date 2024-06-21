<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RapportsRepository;
use App\Repository\ClientsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends AbstractController
{
    /**
     * @Route("/Client/GetMyReports", name="app_client")
     */
    public function index(RapportsRepository $rapportsRepository, ClientsRepository $clientsRepository): JsonResponse
    {

        $id = 1;
        $client = $clientsRepository->find($id);
    
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $reports = $rapportsRepository->findBy(['client' => $client]);
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
}
