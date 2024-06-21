<?php

namespace App\Entity;

use App\Repository\BDSourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BDSourceRepository::class)
 */
class BDSource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $DBTable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDBTable(): ?string
    {
        return $this->DBTable;
    }

    public function setDBTable(?string $DBTable): self
    {
        $this->DBTable = $DBTable;

        return $this;
    }
}
