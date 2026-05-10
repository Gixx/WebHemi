<?php

declare(strict_types=1);

namespace App\Routing;

use App\Entity\SurfaceType;

final readonly class HostContextResolver
{
    public function __construct(
        private HostContextProviderInterface $hostContextProvider,
    ) {
    }

    /**
     * @return array{site_id:int, surface:\App\Entity\SurfaceType, host:string}|null
     */
    public function resolveFromHost(string $host): ?array
    {
        $normalizedHost = strtolower(trim($host));
        if ('' === $normalizedHost) {
            return null;
        }

        $context = $this->hostContextProvider->findContextByHost($normalizedHost);
        if (null === $context) {
            return null;
        }

        $surface = SurfaceType::tryFrom((string) $context['surface']);
        if (null === $surface) {
            return null;
        }

        return [
            'site_id' => (int) $context['site_id'],
            'surface' => $surface,
            'host' => (string) $context['host'],
        ];
    }

    public function resolveCanonicalSiteHost(int $siteId): ?string
    {
        if ($siteId < 1) {
            return null;
        }

        return $this->hostContextProvider->findCanonicalSiteHost($siteId);
    }
}
