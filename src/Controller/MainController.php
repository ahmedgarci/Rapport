<?php

namespace App\Controller;

use App\Helpers;
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
    public function ajouterClient(Request $request, EntityManagerInterface $entityManager, Helpers $help)
    {
        $client = new Clients();
        $entityManager->persist($client);
        $fullname = $request->get('lastname').$request->get('firstname');
        if (empty($fullname) || empty($request->request->get('email')) || empty($request->request->get('password'))) {
            $error = "Remplir Tous Les Champs";
            return $this->render('main/AddClientForm.html.twig', [
                'error' => $error
            ]);
        }elseif($help->verifExitMail($request->request->get('email'),$entityManager) == false){
            $error = "Ce email est deja existe";
            return $this->render('main/AddClientForm.html.twig', [
                'error' => $error,
            ]);
        }else{
        $client->setUsername($fullname);
        $client->setEmail($request->request->get('email'));
        $client->setPassword($request->request->get('password'));
        $entityManager->flush();
        return $this->redirectToRoute('AdminMain');
        }
    }

    /**
     * @Route("admin/client", name="AddClientfrom")
     */
     public function afficherAjouterClient(): Response
     {
        return $this->render('main/AddClientForm.html.twig');
     }

    /**
     * @Route("admin/client{id}", name="suppclient")
     */
     public function supprimerClient($id, EntityManagerInterface $entityManager):Response
     {
        $client = $entityManager->getRepository(Clients::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException('Client not found for id '.$id);
        }
        $entityManager->remove($client);
        $entityManager->flush();
        return $this->redirectToRoute('AdminMain');
     }

}
