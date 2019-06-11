<?php

namespace App\Entity;

use App\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 * @author dev1 -> Ore Richard
 */
class Application
{

    use SoftDeletable;


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $appToken;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $structure;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="smallint", options={"default":1})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserGroup", mappedBy="application", orphanRemoval=true)
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="application")
     */
    private $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="applications")
     */
    private $users;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->applicationUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppToken(): ?string
    {
        return $this->appToken;
    }

    public function setAppToken(string $appToken): self
    {
        $this->appToken = $appToken;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getStructure(): ?string
    {
        return json_decode($this->structure, true);
    }

    public function setStructure(?array $structure): self
    {
        $this->structure = json_encode($structure);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|UserGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups->filter(function (UserGroup $group){
            return $group->getStatus() == UserGroup::$activeStatus;
        });
    }

    public function addGroup(UserGroup $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setApplication($this);
        }

        return $this;
    }

    public function removeGroup(UserGroup $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getApplication() === $this) {
                $group->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setApplication($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getApplication() === $this) {
                $client->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users->filter(function (User $user){
            return $user->getStatus() == User::$activeStatus;
        });
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addApplication($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeApplication($this);
        }

        return $this;
    }

    public function delete(){
        $this->setStatus(self::$deletedStatus);
    }
}
