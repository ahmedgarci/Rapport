<?php

namespace App\Controller;


use App\Entity\Clients;

use App\Entity\Technicien;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Helpers;
class AdminFeaturesController extends AbstractController
{
    /**
     * @Route("/Admin/newUser", name="AddClient")
     */
    public function ajouterClient(Request $request,
                                  TechnicienRepository $technicienRepository,
                                  ClientsRepository $clientsRepository,
                                  Helpers $helpers)
    {
        $UserInfo = json_decode($request->getContent(),true);
        $checkUserExistence = $helpers->SearchUser(email:$UserInfo["email"],id: null);
        if ($checkUserExistence) {
            return  new JsonResponse("Email existe",JSONResponse::HTTP_CONFLICT);
        }
        switch ($UserInfo["Role"]) {
            case "CLIENT":{
                $client = new Clients();
                $client->setUsername($UserInfo["username"])->setEmail($UserInfo["email"])
                    ->setPassword($UserInfo['password']);
                $clientsRepository->add($client, true);
                break;
            }
            case "TECHNICIEN":{
                $Technicien = new Technicien();
                $Technicien->setUsername($UserInfo["username"])->setEmail($UserInfo["email"])
                    ->setPassword($UserInfo['password']);
                $technicienRepository->add($Technicien, true);

                break;
            }
        }
        return new JsonResponse("user created",JsonResponse::HTTP_OK);

    }


    /**
     * @Route("Admin/delete/{id}", name="suppclient")
     */
    public function supprimerClient($id, Helpers $helpers, ClientsRepository $clientsRepository, TechnicienRepository $technicienRepository): Response
    {
        $user = $helpers->searchUser(null, $id);

        if (!$user) {
            return new JsonResponse('User not found for id ' . $id, JsonResponse::HTTP_NOT_FOUND);
        }

        if ($user["type"] == "CLIENT") {
            $clientsRepository->remove($user["user"], true);
            return new JsonResponse('Client supprimé avec succès', Response::HTTP_OK);
        }

        if ($user["type"] == "TECHNICIEN") {
            $technicienRepository->remove($user["user"], true);
            return new JsonResponse('Technicien supprimé avec succès', Response::HTTP_OK);
        }

        return new JsonResponse('Unable to delete user', Response::HTTP_BAD_REQUEST);
    }






}