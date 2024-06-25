<?php
namespace App\Controller;

use App\Entity\Rapports;
use App\Entity\DBSource;
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
use App\Repository\DBSourceRepository;
use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;

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
    //    $userDecodedData = $jwtEncoder->decode($token);
        $userId = $helpers->DecodeToken($token);
        $technician = $tech->findOneBy(["id" => $userId]);
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
     * @Route("/Techniciens/showDemands", name="TechShowDemands", methods={"GET"})
     */
    public function Show(Request $request, JWTEncoderInterface $jwtEncoder,
    Helpers $helpers,
    ClientsRepository $cl,
     TechnicienRepository $techRepo, 
     SerializerInterface $serializer, 
     DBSourceRepository $dbSourceRep, 
     ClientsRepository $clientRep): Response
    {
        try {
 //           $userCookies = $request->cookies->get("user");
 //           if (!$userCookies) {
 //               return new JsonResponse('Unauthorized', Response::HTTP_NOT_FOUND);
 //           }
  //          $userId =$helpers->DecodeToken($userCookies);
            $technicien = $techRepo->findOneBy(["id"=>1]);
            $TechDemands = $dbSourceRep->findBy(["tech"=>$technicien,'isGenerated'=>false]);
            $jsonContent = $serializer->serialize($TechDemands, 'json',['groups'=>["dbSource:db_Read","client:userInfo"]]);
            return new JsonResponse([$jsonContent], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }






}