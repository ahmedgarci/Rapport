<?php
namespace App\Controller;

use App\Helpers;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="app_auth")
     */
    public function index(
        Request $request,
        EntityManagerInterface $em,
        AdminRepository $adminRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $userInfo = $request->getContent();
        // $admins = $adminRepository->findAll();
        //$serializedAdmins = $serializer->serialize($admins, 'json');
         $result =  Helpers::class->SearchUser($userInfo->email);
        return new JsonResponse($userInfo, json: true);


    }
}
