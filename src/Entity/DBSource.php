<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\DBSourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DBSourceRepository::class)
 */
class DBSource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"dbSource:db_Read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Clients::class, inversedBy="dBSources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"dbSource:db_Read","client:userInfo"})
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"dbSource:db_Read"})
     */
    private $driver;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"dbSource:db_Read"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"dbSource:db_Read"})
     */
    private $host;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"dbSource:db_Read"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"dbSource:db_Read"})
     */
    private $DB;

    /**
     * @ORM\ManyToOne(targetEntity=Technicien::class, inversedBy="dBSources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"Tech:TechInfo"})
     */
    private $tech;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isGenerated;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DateFrom;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $DateTo;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"dbSource:db_Read"})
     */
    private $Column1;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"dbSource:db_Read"})
     */
    private $Column2;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Groups({"dbSource:db_Read"})
     */
    private $Column3;

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

    public function getDriver(): ?string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDB(): ?string
    {
        return $this->DB;
    }

    public function setDB(string $DB): self
    {
        $this->DB = $DB;
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

    public function getIsGenerated(): ?bool
    {
        return $this->isGenerated;
    }

    public function setIsGenerated(?bool $isGenerated): self
    {
        $this->isGenerated = $isGenerated;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->DateFrom;
    }

    public function setDateFrom(?\DateTimeInterface $DateFrom): self
    {
        $this->DateFrom = $DateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->DateTo;
    }

    public function setDateTo(\DateTimeInterface $DateTo): self
    {
        $this->DateTo = $DateTo;

        return $this;
    }

    public function getColumn1(): ?string
    {
        return $this->Column1;
    }

    public function setColumn1(?string $Column1): self
    {
        $this->Column1 = $Column1;

        return $this;
    }

    public function getColumn2(): ?string
    {
        return $this->Column2;
    }

    public function setColumn2(?string $Column2): self
    {
        $this->Column2 = $Column2;

        return $this;
    }

    public function getColumn3(): ?string
    {
        return $this->Column3;
    }

    public function setColumn3(?string $Column3): self
    {
        $this->Column3 = $Column3;

        return $this;
    }


}
