<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Repository\ClientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ClientsRepository::class)
 */
class Clients implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"client:userInfo"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"client:userInfo"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"client:userInfo","client:userRelatedFields"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

      /**
     * @ORM\OneToMany(targetEntity=Rapports::class, mappedBy="client")
     * @MaxDepth(1)     
     * @Groups({"client:userRelatedFields"})
     */
    private $rapports;

    /**
     * @ORM\OneToMany(targetEntity=DBSource::class, mappedBy="client_id")
     * @Groups({"client:userRelatedFields"})
     */
    private $dBSources;

    /**
     * @ORM\OneToMany(targetEntity=DataSource::class, mappedBy="client")
     */
    private $dataSources;

    public function __construct()
    {
        $this->rapports = new ArrayCollection();
        $this->dBSources = new ArrayCollection();
        $this->dataSources = new ArrayCollection();
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
            $rapport->setClient($this);
        }

        return $this;
    }

    public function removeRapport(Rapports $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            // set the owning side to null (unless already changed)
            if ($rapport->getClient() === $this) {
                $rapport->setClient(null);
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

    /**
     * @return Collection<int, DBSource>
     */
    public function getDBSources(): Collection
    {
        return $this->dBSources;
    }

    public function addDBSource(DBSource $dBSource): self
    {
        if (!$this->dBSources->contains($dBSource)) {
            $this->dBSources[] = $dBSource;
            $dBSource->setClientId($this);
        }

        return $this;
    }

    public function removeDBSource(DBSource $dBSource): self
    {
        if ($this->dBSources->removeElement($dBSource)) {
            if ($dBSource->getClientId() === $this) {
                $dBSource->setClientId(null);
            }
        }

        return $this;
    }

   
}
