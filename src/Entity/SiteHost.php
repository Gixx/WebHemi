<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteHostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteHostRepository::class)]
#[ORM\Table(name: 'site_host')]
#[ORM\UniqueConstraint(name: 'uniq_site_host_host', columns: ['host'])]
class SiteHost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Site::class, inversedBy: 'hosts')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Site $site;

    #[ORM\Column(length: 191)]
    private string $host;

    #[ORM\Column(length: 16)]
    private string $surface;

    #[ORM\Column(length: 16)]
    private string $status = 'pending';

    #[ORM\Column]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = strtolower(trim($host));

        return $this;
    }

    public function getSurface(): string
    {
        return $this->surface;
    }

    public function setSurface(string $surface): self
    {
        $this->surface = strtolower(trim($surface));

        return $this;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, ['pending', 'verified', 'active'], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid status: %s', $status));
        }

        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
