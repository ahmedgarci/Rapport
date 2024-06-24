<?php

namespace App\Controller;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use DateTime;
use App\Helpers;
use DateTimeZone;
use App\Entity\Clients;
use App\Entity\Rapports;
use App\Entity\Technicien;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/Admin", name="AdminMain",methods="GET")
     */
    public function index(ClientsRepository $clientsRep, SerializerInterface $serializer ,
                          TechnicienRepository $techRep):JsonResponse
    {
        $serializedClients = json_decode($serializer->serialize($clientsRep->findAll(), 'json',['groups'=>"client:userInfo"]));
        $serializedTechs= json_decode($serializer->serialize($techRep->findAll(), 'json',['groups'=>"Tech:TechInfo"]));
        return new JsonResponse([
            "Techs"=>$serializedTechs,
            "Clients"=>$serializedClients
        ]);
    }

}