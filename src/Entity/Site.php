<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
#[ORM\Table(name: 'site')]
#[ORM\UniqueConstraint(name: 'uniq_site_slug', columns: ['slug'])]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id = null;

    #[ORM\Column(length: 64)]
    private string $slug;

    #[ORM\Column(length: 128)]
    private string $name;

    #[ORM\Column]
    private bool $isEnabled = true;

    /**
     * @var Collection<int, SiteHost>
     */
    #[ORM\OneToMany(targetEntity: SiteHost::class, mappedBy: 'site', cascade: ['persist'], orphanRemoval: true)]
    private Collection $hosts;

    public function __construct()
    {
        $this->hosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = strtolower(trim($slug));

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return Collection<int, SiteHost>
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }
}
