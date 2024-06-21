<?php

namespace App\Controller;

use App\Entity\Rapports;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
use App\Repository\RapportsRepository;
use Symfony\Component\Serializer\SerializerInterface;
use FPDF;
use App\Repository\DBSourceRepository;
use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use App\Entity\DBSource;

class TechnicienFeaturesController extends AbstractController
{

    /**
     * @Route("/Techniciens/PublishReport", name="app_technicien")
     */
    public function index(
        Request $request,
        RapportsRepository $rapportsRepository,
        Helpers $helpers,
        ClientsRepository $clientsRepository,
        TechnicienRepository $tech,
        JWTEncoderInterface $jwtEncoder,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $token = $request->cookies->get("user");
        if (!$token) {
            return new JsonResponse("Unauthorized", Response::HTTP_UNAUTHORIZED);
        }

        $Receiver = $request->request->get("email");
        $client = $clientsRepository->findOneBy(["email" => "ahmedgarci146@gmail.com"]);
        if (!$client || $client == null) {
            return new JsonResponse('client introuvable', Response::HTTP_BAD_REQUEST);
        }
        $uploadedFile = $request->files->get("file");
        $titre = $request->request->get("title");
        $userDecodedData = $jwtEncoder->decode($token);
        $technician = $tech->findOneBy(["id" => $userDecodedData["id"]]);
        $rapport = new Rapports();
        $uploadsDirectory = $this->getParameter('upload_directory');
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move($uploadsDirectory, $newFilename);
        $rapport->setTitle($titre);
        $rapport->setClient($client);
        $rapport->setTech($technician);
        $rapport->setReportPath($newFilename);
        $rapportsRepository->add($rapport, true);
        return new JsonResponse("Report Sent To The Specific Client", Response::HTTP_OK);
    }


     /**
     * @Route("/Techniciens/showDemands", name="dbdemande", methods={"GET"})
     */
    public function Show(Request $request, JWTEncoderInterface $jwtEncoder, TechnicienRepository $techRepo, SerializerInterface $serializer, DBSourceRepository $dbSourceRep, ClientsRepository $clientRep)
    {
        try {
            $dem = $dbSourceRep->findBy(['Tech' => 5]);
            $jsonContent = $serializer->serialize($dem, 'json', ['groups' => 'show_technicien']);
            dd($dem);
            return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
      } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }












}