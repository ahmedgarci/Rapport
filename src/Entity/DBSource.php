<?php

namespace App\Entity;

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
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Clients::class, inversedBy="dBSources")
     */
    private $client_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $driver;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $host;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DB;

    /**
     * @ORM\ManyToOne(targetEntity=Technicien::class, inversedBy="dBSources")
     */
    private $Tech;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?Clients
    {
        return $this->client_id;
    }

    public function setClientId(?Clients $client_id): self
    {
        $this->client_id = $client_id;

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
        return $this->Tech;
    }

    public function setTech(?Technicien $Tech): self
    {
        $this->Tech = $Tech;

        return $this;
    }
}
