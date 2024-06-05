<?php
namespace App\Controller;

use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="app_auth")
     */
    public function index(
        EntityManagerInterface $em,
        AdminRepository $adminRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $admins = $adminRepository->findAll();
        $serializedAdmins = $serializer->serialize($admins, 'json');

        return new JsonResponse($serializedAdmins, json: true);
    }
}

/** dddddddddddddd  hazemm