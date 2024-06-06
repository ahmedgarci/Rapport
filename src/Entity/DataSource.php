<?php

namespace App\Entity;

use App\Repository\DataSourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DataSourceRepository::class)
 */
class DataSource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Technicien::class, inversedBy="dataSources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tech;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Url;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->Url;
    }

    public function setUrl(?string $Url): self
    {
        $this->Url = $Url;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }
}
