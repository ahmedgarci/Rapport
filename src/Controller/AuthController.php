<?php
namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="app_auth")
     */
    public function index(
        JWTEncoderInterface $JWTEncoder,
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        JWTTokenManagerInterface $JWTManager,
        Helpers $helpers
    ): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);
        $Exists = $helpers->SearchUser($userData["email"]);
        if(!$Exists){
            return new JsonResponse("User Not Found",JsonResponse::HTTP_NOT_FOUND ,json: true);
        }
        $isValid = $passwordEncoder->isPasswordValid($Exists['user'],$userData['password']);
        if(!$isValid){
            return  new JsonResponse("Invalid Credentials",JsonResponse::HTTP_UNAUTHORIZED);
        }
        $token = $JWTManager->create($Exists['user']);
    //    $decodedToken = $JWTEncoder->decode($token);

        setcookie("user",$token,3600);
        return new JsonResponse($Exists["type"], json: true);

    }










}
