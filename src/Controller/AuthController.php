<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Helpers;
use App\Repository\TechnicienRepository;
use App\Repository\AdminRepository;
use App\Entity\Admin;
use App\Entity\Technicien;

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
        Helpers $helpers,
        TechnicienRepository $repository,
        AdminRepository $adminRepository
    ): JsonResponse
    {
        //$admin->setPassword("ahmed")->setEmail("garci@gmail.com")
        //    ->setUsername("aaa");
        $userData = json_decode($request->getContent(), true);
        $Exists = $helpers->SearchUser($userData["email"],null);
     //   return new JsonResponse($JWTEncoder->decode($request->cookies->get("user")));
        if(!$Exists){
            return new JsonResponse("User Not Found",JsonResponse::HTTP_NOT_FOUND ,json: true);
        }
        $isValid = $passwordEncoder->isPasswordValid($Exists["user"],$userData["password"]);
        if(!$isValid){
            return  new JsonResponse("Invalid Credentials",JsonResponse::HTTP_UNAUTHORIZED);
        }
        $payload = [
            'username' => $Exists["user"]->getUsername(),
            'id' => $Exists["user"]->getId(),  // Inclure l'ID de l'utilisateur dans le payload
            // Autres informations pertinentes à inclure...
        ];
        $token = $JWTManager->create($Exists['user'],$payload);
        $response = new JsonResponse($Exists['type']);
        $cookie = new Cookie(
            'user',
            $token,
            time() + 3600,
            '/',
            null,
            false,
            true,
            false,
            'lax'
        );
        $response->headers->setCookie($cookie);
        return $response;

    }










}
