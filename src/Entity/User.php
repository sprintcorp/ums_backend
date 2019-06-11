<?php

namespace App\Entity;

use App\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @author dev1 -> Ore Richard

 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{

    use SoftDeletable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $otherNames;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $email;

    /**
     * @ORM\Column(type="smallint", options={"default":1})
     */
    private $status;

    /**
     * @ORM\Column(type="text")
     */
    private $password;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", inversedBy="users")
     * @ORM\JoinTable(
     *  name="user_group",
     *  joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *  }
     * )
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Application", inversedBy="users")
     */
    private $applications;

    /**
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->applications = new ArrayCollection();
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }

    public function getRoles(): ?string
    {
        // Roles must be de-serialized.
        return $this->roles;
    }

    public function setRoles(string $roles): self
    {
        // Roles must be serialized.
        $this->roles = $roles;

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

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
        }

        return $this;
    }

    public function removeGroup(UserGroup $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOtherNames(): ?string
    {
        return $this->otherNames;
    }

    public function setOtherNames(?string $otherNames): self
    {
        $this->otherNames = $otherNames;

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications->filter(function (Application $application){
            return $application->getStatus() == Application::$activeStatus;
        });
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
        }

        return $this;
    }


    /**
     * Checks if a user is registered on an application.
     * @param Application $application
     * @return bool
     */
    public function hasApplication(Application $application): bool
    {
        return $this->getApplications()->contains($application);
    }
}
