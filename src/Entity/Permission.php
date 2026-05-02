<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: 'rbac_permission')]
#[ORM\UniqueConstraint(name: 'uniq_rbac_permission_name', columns: ['name'])]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 128)]
    private string $name;

    #[ORM\Column(length: 128)]
    private string $label;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = strtolower(trim($name));

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = trim($label);

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addPermission($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removePermission($this);
        }

        return $this;
    }
}
