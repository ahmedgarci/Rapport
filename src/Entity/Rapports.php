<?php

namespace App\Entity;

use App\Repository\RapportsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Clients;
use App\Entity\Technicien;

/**
 * @ORM\Entity(repositoryClass=RapportsRepository::class)
 */
class Rapports
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Clients::class, inversedBy="rapports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Technicien::class, inversedBy="rapports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tech;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reportPath;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTech(): ?Technicien
    {
        return $this->tech;
    }

    public function setTech(?Technicien $tech): self
    {
        $this->tech = $tech;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReportPath(): ?string
    {
        return $this->reportPath;
    }

    public function setReportPath(string $reportPath): self
    {
        $this->reportPath = $reportPath;

        return $this;
    }

}
