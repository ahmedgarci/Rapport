<?php

namespace App;

use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;

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
    public function SearchUser ($email)
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
}