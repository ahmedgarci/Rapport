<?php

namespace App;

use App\Entity\Clients;
use App\Entity\Technicien;
use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;

class Helpers
{
    private $technicienRepository;
    private $adminRepository;
    private  $clientsRepository;
    public function __construct(
    AdminRepository $AdminRepository,
    ClientsRepository $ClientsRepository,
    TechnicienRepository $TechnicienRepository
) {
    $this->adminRepository = $AdminRepository;
    $this->clientsRepository = $ClientsRepository;
    $this->technicienRepository = $TechnicienRepository;
}
    public function SearchUser($email)
    {
        try {
            $isAdmin = $this->adminRepository->findOneBy(["email" => $email]);
            if ($isAdmin) {
                return [
                    'type'=>"ADMIN",
                    'user'=>$isAdmin
                    ];
                }

            $isClient = $this->clientsRepository->findOneBy(["email" => $email]);
            if ($isClient) {
                return [
                    'type'=>"CLIENT",
                    "user"=>$isClient
                    ];
            }

            $isTechnicien = $this->technicienRepository->findOneBy(["email" => $email]);
            if ($isTechnicien) {
                return [
                    'type'=>"TECHNICIEN",
                    "user"=>$isTechnicien
                    ];
            }

            return null;
        }catch (Exception $e){
            error_log($e->getMessage());
        }
    }

public function verifExitMail(String $mail , EntityManagerInterface $entityManager ):bool
{
    $clientRep = $entityManager->getRepository(Clients::class);
    $technicienRep = $entityManager->getRepository(Technicien::class);
    
    $testclient = $clientRep->findOneBy(['email' => $mail]);
    $testtech = $technicienRep->findOneBy(['email' => $mail]);
    if((!$testclient) || (!$testtech)){
        return false;
    }else{
        return true;
    }
}



}