<?php

namespace App;

use Exception;

use App\Repository\AdminRepository;
use App\Repository\ClientsRepository;
use App\Repository\TechnicienRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class Helpers
{
    private $technicienRepository;
    private $adminRepository;
    private $clientsRepository;
    private $jwt;
    public function __construct(AdminRepository $adminRepository, ClientsRepository $clientsRepository,
                             JWTEncoderInterface $jwtEncoder,TechnicienRepository $technicienRep)
    {
        $this->adminRepository = $adminRepository;
        $this->clientsRepository = $clientsRepository;
        $this->technicienRepository = $technicienRep;
        $this->jwt = $jwtEncoder;
    }

    public function searchUser($email=null, $id=null)
    {
        $criteria = [];
        try {
            if ($email !== null) {
                $criteria["email"] = $email;
            }
            if ($id !== null) {
                $criteria["id"] = $id;
            }
            $isAdmin = $this->adminRepository->findOneBy($criteria);
            if ($isAdmin) {
                return [
                    'type' => "ADMIN",
                    'user' => $isAdmin
                ];}

            $isClient = $this->clientsRepository->findOneBy($criteria);
            if ($isClient) {
                return [
                    'type' => "CLIENT",
                    'user' => $isClient
                ];}
            $isTechnicien = $this->technicienRepository->findOneBy($criteria);
            if ($isTechnicien) {
                return [
                    'type' => "TECHNICIEN",
                    'user' => $isTechnicien
                ];}

            return null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
}


    public function DecodeToken($Token){
      $data = $this->jwt->decode($Token);
        return $data["id"];
    }
 
    

















    
}