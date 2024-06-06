<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/admin", name="AdminMain")
     */
    public function index(ClientsRepository $clientsRep , TechnicienRepository $techRep): Response
    {
        $client = $clientsRep->findAll();
        $tech = $techRep->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'clients' => $client,
            'techs' => $tech,
        ]);
    }

    /**
     * @Route("/client/addClient", name="AddClient", methods={"POST"})
     */
    public function ajouterClient(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Clients();
        $client->setUsername($request->request->get('username'));
        $client->setEmail($request->request->get('email'));
        $client->setPassword($request->request->get('password'));
    
        $entityManager->persist($client);
        $entityManager->flush();

        return $this->redirectToRoute('AdminMain');
    }
}