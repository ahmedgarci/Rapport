<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class TechnicienFeaturesController extends AbstractController
{
    public  function __construct ()
    {

    }
    /**
     * @Route("/Techniciens/PublishReport", name="app_technicien")
     */
    public function index(
        Request $request,
        JWTTokenManagerInterface $JWTTokenManager,
        TokenStorageInterface $tokenStorageInterface,
        jwtEncoderInterface $jwtEncoder
    ): JsonResponse
    {
        $file = $request->files->all();
       $user= $request->cookies->get("user");
//          return new JsonResponse($JWTEncoder->decode($request->cookies->get("user")));

        return  new JsonResponse(json_decode($request->getContent(),true)
            ,Response::HTTP_OK);

    }
}
