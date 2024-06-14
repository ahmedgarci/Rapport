<?php

namespace App\Controller;



use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/Admin", name="AdminMain",methods="GET")
     */
    public function index(ClientsRepository $clientsRep, SerializerInterface $serializer ,

                          TechnicienRepository $techRep):JsonResponse
    {

        $serializedClients = json_decode($serializer->serialize($clientsRep->findAll(), 'json'));
        $serializedTechs= json_decode($serializer->serialize($techRep->findAll(), 'json'));
        return new JsonResponse([
            "Techs"=>$serializedTechs,
            "Clients"=>$serializedClients
        ]);
    }


}