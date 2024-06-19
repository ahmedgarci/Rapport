<?php

namespace App\Controller;

use App\Entity\Rapports;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
use App\Repository\RapportsRepository;
use Symfony\Component\Serializer\SerializerInterface;
use FPDF;

class TechnicienFeaturesController extends AbstractController
{
    public  function __construct (SerializerInterface $serializer ,)
    {

    }


    /**
     * @Route("/Techniciens/PublishReport", name="app_technicien")
     */
    public function index(
        Request             $request,
        RapportsRepository $rapportsRepository,
        Helpers             $helpers,
        jwtEncoderInterface $jwtEncoder,
    ): JsonResponse
    {
        $token = $request->cookies->get("user");
        if(!$token){
            return new JsonResponse("Unauthorized",Response::HTTP_UNAUTHORIZED);
        }
        $Receiver = $request->request->get("email");
        $ClientReceiver = $helpers->searchUser($Receiver, null);
        if (!$ClientReceiver || $ClientReceiver == null) {
            return new JsonResponse('client introuvable', Response::HTTP_BAD_REQUEST);
        }
        $uploadedFile = $request->files->get("file");
        $titre = $request->request->get("title");
        $userDecodedData = $jwtEncoder->decode($token);
        $TechnicienSender = $helpers->searchUser(null,$userDecodedData['id']);
        $rapport = new Rapports();
        $rapport->setTitle($titre);
        $uploadsDirectory = $this->getParameter('upload_directory');
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move($uploadsDirectory, $newFilename);
        $rapport->setContent($newFilename)->setTech($TechnicienSender)->setClient($ClientReceiver);
        $rapportsRepository->add($rapport, true);
        return new JsonResponse(" Report Sent To The Specific Client ", Response::HTTP_OK);
    }
    /**
     * @Route("/Techniciens/download", name="download_file")
     */
    public function download(LoggerInterface $logger)
    {
        try {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, "hello world");

            $pdfContent = $pdf->Output("S");
            $logger->info('PDF generated successfully');
            return new Response($pdfContent, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="example.pdf"',
            ]);
        } catch (\Exception $e) {
            $logger->error('Error generating PDF: ' . $e->getMessage());

            return new Response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }










}