<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=TechnicienRepository::class)
 */
class Technicien implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=DataSource::class, mappedBy="tech")
     */
    private $dataSources;

    /**
     * @ORM\OneToMany(targetEntity=Rapports::class, mappedBy="tech")
     */
    private $rapports;

    public function __construct()
    {
        $this->dataSources = new ArrayCollection();
        $this->rapports = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password,PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * @return Collection<int, DataSource>
     */
    public function getDataSources(): Collection
    {
        return $this->dataSources;
    }

    public function addDataSource(DataSource $dataSource): self
    {
        if (!$this->dataSources->contains($dataSource)) {
            $this->dataSources[] = $dataSource;
            $dataSource->setTech($this);
        }

        return $this;
    }

    public function removeDataSource(DataSource $dataSource): self
    {
        if ($this->dataSources->removeElement($dataSource)) {
            // set the owning side to null (unless already changed)
            if ($dataSource->getTech() === $this) {
                $dataSource->setTech(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rapports>
     */
    public function getRapports(): Collection
    {
        return $this->rapports;
    }

    public function addRapport(Rapports $rapport): self
    {
        if (!$this->rapports->contains($rapport)) {
            $this->rapports[] = $rapport;
            $rapport->setTech($this);
        }

        return $this;
    }

    public function removeRapport(Rapports $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            // set the owning side to null (unless already changed)
            if ($rapport->getTech() === $this) {
                $rapport->setTech(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }



}
