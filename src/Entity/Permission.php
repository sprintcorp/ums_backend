<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 * @author dev1 -> Ore Richard
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $mask;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserGroup", mappedBy="permission", cascade={"persist", "remove"})
     */
    private $group;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMask(): ?string
    {
        return $this->mask;
    }

    public function setMask(string $mask): self
    {
        $this->mask = $mask;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

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

    public function getGroup(): ?UserGroup
    {
        return $this->group;
    }

    public function setGroup(?UserGroup $group): self
    {
        $this->groups = $group;

        // set (or unset) the owning side of the relation if necessary
        $newPermission = $group === null ? null : $this;
        if ($newPermission !== $group->getPermission()) {
            $group->setPermission($newPermission);
        }

        return $this;
    }
}
