<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteAssignmentRepository::class)]
#[ORM\Table(name: 'site_assignment')]
#[ORM\UniqueConstraint(name: 'uniq_site_assignment_user_site', columns: ['user_id', 'site_id'])]
class SiteAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'siteAssignments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Site::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Site $site;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}
