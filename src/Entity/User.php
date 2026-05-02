<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[ORM\UniqueConstraint(name: 'uniq_app_user_email', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const AVATAR_TYPE_DEFAULT = 'default';
    public const AVATAR_TYPE_GRAVATAR = 'gravatar';
    public const AVATAR_TYPE_UPLOAD = 'upload';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 191)]
    private string $email = '';

    #[ORM\Column(length: 255)]
    private string $passwordHash = '';

    #[ORM\Column(length: 16)]
    private string $avatarType = self::AVATAR_TYPE_DEFAULT;

    #[ORM\Column(length: 255, nullable: true)]
    private string|null $avatarPath = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'userRoles')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower(trim($email));

        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = trim($passwordHash);

        return $this;
    }

    public function getAvatarType(): string
    {
        return $this->avatarType;
    }

    public function setAvatarType(string $avatarType): self
    {
        $normalized = strtolower(trim($avatarType));
        $allowedTypes = [self::AVATAR_TYPE_DEFAULT, self::AVATAR_TYPE_GRAVATAR, self::AVATAR_TYPE_UPLOAD];
        if (!in_array($normalized, $allowedTypes, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid avatar type: %s', $avatarType));
        }

        $this->avatarType = $normalized;

        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(string|null $avatarPath): self
    {
        $normalizedPath = null;
        if (is_string($avatarPath)) {
            $trimmed = trim($avatarPath);
            $normalizedPath = '' === $trimmed ? null : $trimmed;
        }

        $this->avatarPath = $normalizedPath;

        return $this;
    }

    public function getAvatarUrl(): string
    {
        if (self::AVATAR_TYPE_UPLOAD === $this->avatarType) {
            if (is_string($this->avatarPath)) {
                if (
                    str_starts_with($this->avatarPath, 'http://')
                    || str_starts_with($this->avatarPath, 'https://')
                    || str_starts_with($this->avatarPath, '/')
                ) {
                    return $this->avatarPath;
                }

                return '/' . $this->avatarPath;
            }

            return '/assets/admin/icons/avatar/default-male.svg';
        }

        if (self::AVATAR_TYPE_GRAVATAR === $this->avatarType) {
            $hash = md5(strtolower(trim($this->email)));

            return sprintf('https://www.gravatar.com/avatar/%s?d=mp&s=96', $hash);
        }

        return '/assets/admin/icons/avatar/default-male.svg';
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        foreach ($this->roles as $role) {
            $roles[] = $role->getName();
        }

        return array_values(array_unique($roles));
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoleEntities(): Collection
    {
        return $this->roles;
    }

    public function hasPermission(string $permission): bool
    {
        $normalizedPermission = strtolower(trim($permission));

        if ('' === $normalizedPermission) {
            return false;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($normalizedPermission)) {
                return true;
            }
        }

        return false;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addUserRole($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removeUserRole($this);
        }

        return $this;
    }
}
