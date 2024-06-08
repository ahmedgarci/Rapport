<?php

namespace App\Controller;

use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProfileController extends AbstractController
{
     /**
     * @Route("admin/profile", name="AdminProfile")
     */
    public function getinfoProfile(AdminRepository $adminRepository): Response
    {
        $infos = $adminRepository->findAll();
        return $this->render('main/Profile.html.twig', [
            'controller_name' => 'AdminProfileController',
            'infos' => $infos,
        ]);
    }

    /**
     * @Route("admin/profile{id}", name="updateprofile")
     */
    public function updateProfileInfo(int $id, Request $request, AdminRepository $adminRepository, EntityManagerInterface $entityManager):Response{

        $admin = $adminRepository->find($id);
            $password = $request->request->get('password');

            $admin->setUsername($request->get('username'));
            $admin->setEmail($request->request->get('email'));
            if ($password) {
                $encodedPassword = password_hash($password, PASSWORD_BCRYPT);
                $admin->setPassword($encodedPassword);
            }

            $entityManager->persist($admin);
            $entityManager->flush();
            return $this->redirectToRoute('AdminProfile');
    }

//hedhom yestanew f lauth bch y5dmmo be l'id ta3 ladmin li connectee
}
