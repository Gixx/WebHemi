<?php

declare(strict_types=1);

namespace App\Routing;

use App\Repository\SiteHostRepository;

final readonly class DoctrineHostContextProvider implements HostContextProviderInterface
{
    public function __construct(
        private SiteHostRepository $siteHostRepository,
    ) {
    }

    #[\Override]
    public function findContextByHost(string $normalizedHost): ?array
    {
        return $this->siteHostRepository->findContextByHost($normalizedHost);
    }
}
