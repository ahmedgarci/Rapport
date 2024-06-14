<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Helpers;
use App\Entity\Clients;
use App\Entity\Rapports;
use App\Entity\Technicien;
use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/Admin", name="AdminMain",methods={"GET"})
     */
    public function index(
        ClientsRepository $clientsRep,
        SerializerInterface $serializer,
        TechnicienRepository $techRep
    ): JsonResponse {

        $serializedClients = $serializer->serialize($clientsRep->findAll(), 'json');
        $serializedTechs = $serializer->serialize($techRep->findAll(), 'json');
        return new JsonResponse([
            "Clients" => $serializedClients,
            "techs" => $serializedTechs
        ]);
    }

    /**
     * @Route("/client/addClient", name="AddClient", methods={"POST"})
     */
    public function ajouterClient(
        Request $request,
        EntityManagerInterface $entityManager,
        Helpers $help
    ) {


        if (empty($request->request->get('email')) || empty($request->request->get('password'))) {
            $error = "Remplir Tous Les Champs";

        }
        $checkUserExistence = $help->SearchUser($request->request->get('email'));
        if ($checkUserExistence) {


            $fullname = $request->get('lastname') . $request->get('firstname');
            if (empty($fullname) || empty($request->request->get('email')) || empty($request->request->get('password'))) {
                $error = "Remplir Tous Les Champs";
                return $this->render('main/AddClientForm.html.twig', [
                    'error' => $error
                ]);
            } elseif ($help->SearchUser($request->request->get('email'))) {

                $error = "Ce email est deja existe";
            } else {

                $client = new Clients();
                $fullname = $request->get('lastname') . $request->get('firstname');
                $client->setUsername($fullname)->setEmail($request->request->get('email'))
                    ->setPassword($request->request->get('password'));
                $entityManager->persist($client);
                $entityManager->flush();
                return $this->redirectToRoute('AdminMain');
            }
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
    public function supprimerClient($id, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Clients::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException('Client not found for id ' . $id);
        }
        $entityManager->remove($client);
        $entityManager->flush();
        return $this->redirectToRoute('AdminMain');
    }

    /**
     * @Route("/technicien/addTechnicien", name="AddTech", methods={"POST"})
     */
    public function ajouterTech(Request $request, EntityManagerInterface $entityManager, Helpers $help)
    {

        $fullname = $request->get('lastname') . $request->get('firstname');
        if (empty($fullname) || empty($request->request->get('email')) || empty($request->request->get('password'))) {
            $error = "Remplir Tous Les Champs";
            return $this->render('main/AddClientForm.html.twig', [
                'error' => $error
            ]);
        } elseif ($help->SearchUser($request->request->get('email'))) {
            $error = "Ce email est deja existe";
            return $this->render('main/AddClientForm.html.twig', [
                'error' => $error,
            ]);
        } else {
            $technicien = new Technicien();
            $entityManager->persist($technicien);
            $technicien->setUsername($fullname);
            $technicien->setEmail($request->request->get('email'));
            $technicien->setPassword($request->request->get('password'));
            $entityManager->flush();
            return $this->redirectToRoute('AdminMain');
        }
    }

    /**
     * @Route("admin/technicien", name="AddTechnicienfrom")
     */
    public function afficherAjouterTechnicien(): Response
    {
        return $this->render('main/AddTechForm.html.twig');
    }
    /**
     * @Route("admin/technicien{id}", name="suppTech")
     */
    public function supprimeTechnicien($id, EntityManagerInterface $entityManager): Response
    {
        $Tech = $entityManager->getRepository(Technicien::class)->find($id);
        if (!$Tech) {
            throw $this->createNotFoundException('Client not found for id ' . $id);
        }
        $entityManager->remove($Tech);
        $entityManager->flush();
        return $this->redirectToRoute('AdminMain');
    }

    /**
     * @Route("admin/clients/update{id}", name="clients")
     */
    public function getClients(int $id, Request $request, ClientsRepository $client, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Clients::class)->find($id);
        return $this->render('main/editClients.html.twig', [
            'controller_name' => 'MainController',
            'client' => $client,
        ]);
    }

    /**
     * @Route("/admin/clients/update/save/{uid}", name="updateclients", methods={"POST"})
     */
    public function updateclientsProfile(int $uid, Request $request, ClientsRepository $clientRepository, EntityManagerInterface $entityManager, Helpers $help): Response
    {
        $client = $clientRepository->find($uid);
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        } elseif ($help->SearchUser($request->request->get('email')) && ($client->getEmail() != $request->request->get('email'))) {
            $error = "Ce email est deja existe";
            return $this->render('main/editClients.html.twig', [
                'error' => $error,
                'client' => $client
            ]);
        } else {

            $password = $request->request->get('password');
            $client->setUsername($request->request->get('username'));
            $client->setEmail($request->request->get('email'));

            if ($password) {
                $encodedPassword = password_hash($password, PASSWORD_BCRYPT);
                $client->setPassword($encodedPassword);
            }

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('AdminMain');
        }
    }

    /**
     * @Route("/admin/technicien/update/{id}", name="getTech")
     */
    public function gettech(int $id, Request $request, TechnicienRepository $technicienRepository, EntityManagerInterface $entityManager): Response
    {
        $techni = $entityManager->getRepository(Technicien::class)->find($id);
        return $this->render('main/editTech.html.twig', [
            'controller_name' => 'MainController',
            'techni' => $techni,
        ]);
    }

    /**
     * @Route("/admin/technicien/update/save/{uid}", name="updatetech", methods={"POST"})
     */
    public function updateTechProfile(int $uid, Request $request, TechnicienRepository $technicienRepository, EntityManagerInterface $entityManager, Helpers $help): Response
    {
        $techni = $technicienRepository->find($uid);

        if (!$techni) {
            throw $this->createNotFoundException('Client not found');

        } elseif ($help->SearchUser($request->request->get('email')) && ($techni->getEmail() != $request->request->get('email'))) {
            $error = "Ce email est deja existe";
            return $this->render('main/editTech.html.twig', [
                'error' => $error,
                'techni' => $techni
            ]);
        } else {
            $password = $request->request->get('password');
            $techni->setUsername($request->request->get('username'));
            $techni->setEmail($request->request->get('email'));

            if ($password) {
                $encodedPassword = password_hash($password, PASSWORD_BCRYPT);
                $techni->setPassword($encodedPassword);
            }

            $entityManager->persist($techni);
            $entityManager->flush();

            return $this->redirectToRoute('AdminMain');
        }
    }



    /**
     * @Route("/technicien/send", name="showsendRapport")
     */
    public function show(): Response
    {
        return $this->render('main/send.html.twig', [
            'controller_name' => 'GenRapporController',
        ]);
    }


    /**
     * @Route("/gen/send/rapport", name="stockRapport", methods={"POST"})
     */
    public function send(Request $req, EntityManagerInterface $entity)
    {

    $idd= 2;
    $technicient = $this->gettechnicien($idd,$entity);




    $client = $this->getClient($req->request->get('email'), $entity);
    if ($client === null) {
        return new Response('client introuvable', Response::HTTP_BAD_REQUEST);
    }
    $rapport = new Rapports();
    $rapport->setTitle($req->request->get('title'));
    $uploadedFile = $req->files->get('file');
    $uploadsDirectory = $this->getParameter('upload_directory');
    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
    $uploadedFile->move($uploadsDirectory, $newFilename);
    $rapport->setContent($newFilename);

    $rapport->setTech($technicient);

    $rapport->setClient($client);
    $entity->persist($rapport);
    $entity->flush();
    return new Response('bieeen');
    }



    /**
     * @Route("/client/getrap/{id}", name="getrapp", methods={"GET"})
     */
    public function getRapport(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $rapports = $entityManager->getRepository(Rapports::class)->findBy(['client' => $id]);
        $rapportData = [];
        foreach ($rapports as $rapport) {
            $file = stream_get_contents($rapport->getContent());
            $rapportData[] = [
                'id' => $rapport->getId(),
                'title' => $rapport->getTitle(),
                'rapport' => $file,
            ];
        }
        return new JsonResponse($rapportData);
    }




    private function getClient(string $email, EntityManagerInterface $em): ?Clients
    {
        return $em->getRepository(Clients::class)->findOneBy(['email' => $email]);
    }



    private function gettechnicien(int $id, EntityManagerInterface $em): ?Technicien
    {
        return $em->getRepository(Technicien::class)->findOneBy(['id' => $id]);
    }







}
