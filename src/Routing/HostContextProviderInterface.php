<?php

declare(strict_types=1);

namespace App\Routing;

interface HostContextProviderInterface
{
    /**
     * @return array{site_id:int, surface:string, host:string}|null
     */
    public function findContextByHost(string $normalizedHost): ?array;

    public function findCanonicalSiteHost(int $siteId): ?string;
}
