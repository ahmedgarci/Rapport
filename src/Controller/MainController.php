<?php

namespace App\Controller;

use App\Helpers;
use App\Entity\Clients;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/Admin", name="AdminMain",methods={"GET"})
     */
    public function index(ClientsRepository $clientsRep ,
                          SerializerInterface $serializer,
                          TechnicienRepository $techRep):JsonResponse
    {

        $serializedClients = $serializer->serialize($clientsRep->findAll(), 'json');
        $serializedTechs= $serializer->serialize($techRep->findAll(), 'json');
        return new JsonResponse([
            "Clients"=>$serializedClients,
            "techs"=>$serializedTechs]);
    }

    /**
     * @Route("/client/addClient", name="AddClient", methods={"POST"})
     */
    public function ajouterClient(Request $request,
                                  EntityManagerInterface $entityManager,
                                  Helpers $help)
    {

        if (empty($request->request->get('email')) || empty($request->request->get('password'))) {
            $error = "Remplir Tous Les Champs";

        }
        $checkUserExistence = $help->SearchUser($request->request->get('email'));
        if($checkUserExistence){
            $error = "Ce email est deja existe";
        }else{
            $client = new Clients();
            $fullname = $request->get('lastname').$request->get('firstname');
            $client->setUsername($fullname)->setEmail($request->request->get('email'))
                ->setPassword($request->request->get('password'));
            $entityManager->persist($client);
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
