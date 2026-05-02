<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'rbac_role')]
#[ORM\UniqueConstraint(name: 'uniq_rbac_role_name', columns: ['name'])]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    /** Symfony-compatible role name, e.g. ROLE_ADMIN */
    #[ORM\Column(length: 64)]
    private string $name = '';

    #[ORM\Column(length: 128)]
    private string $label = '';

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $userRoles;

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'role_permission')]
    private Collection $permissions;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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
        $this->name = strtoupper(trim($name));

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
     * @return Collection<int, User>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        $normalizedPermission = strtolower(trim($permission));

        if ('' === $normalizedPermission) {
            return false;
        }

        foreach ($this->permissions as $assignedPermission) {
            if ($assignedPermission->getName() === $normalizedPermission) {
                return true;
            }
        }

        return false;
    }

    public function addUserRole(User $user): self
    {
        if (!$this->userRoles->contains($user)) {
            $this->userRoles->add($user);
            $user->addRole($this);
        }

        return $this;
    }

    public function removeUserRole(User $user): self
    {
        if ($this->userRoles->removeElement($user)) {
            $user->removeRole($this);
        }

        return $this;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->addRole($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            $permission->removeRole($this);
        }

        return $this;
    }
}
