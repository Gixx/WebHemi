<?php

declare(strict_types=1);

namespace App\Tests\Unit\Routing;

use App\Routing\HostContextProviderInterface;
use App\Routing\HostContextResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HostContextResolverTest extends TestCase
{
    #[Test]
    public function resolvesKnownHost(): void
    {
        $resolver = new HostContextResolver($this->provider([
            'admin.mysite.local' => ['site_id' => 1, 'surface' => 'admin'],
        ]));

        $result = $resolver->resolveFromHost('ADMIN.MYSITE.LOCAL');

        self::assertNotNull($result);
        self::assertSame(1, $result['site_id']);
        self::assertSame('admin', $result['surface']);
        self::assertSame('admin.mysite.local', $result['host']);
    }

    #[Test]
    public function returnsNullForUnknownHost(): void
    {
        $resolver = new HostContextResolver($this->provider([
            'mysite.local' => ['site_id' => 1, 'surface' => 'site'],
        ]));

        self::assertNull($resolver->resolveFromHost('missing.mysite.local'));
    }

    #[Test]
    public function resolvesCanonicalSiteHost(): void
    {
        $resolver = new HostContextResolver($this->provider([], [
            1 => 'mysite.local',
        ]));

        self::assertSame('mysite.local', $resolver->resolveCanonicalSiteHost(1));
        self::assertNull($resolver->resolveCanonicalSiteHost(999));
    }

    /**
     * @param array<string, array{site_id:int, surface:string}> $map
     * @param array<int, string> $canonicalHosts
     */
    private function provider(array $map, array $canonicalHosts = []): HostContextProviderInterface
    {
        return new class ($map, $canonicalHosts) implements HostContextProviderInterface {
            /**
             * @param array<string, array{site_id:int, surface:string}> $map
             * @param array<int, string> $canonicalHosts
             */
            public function __construct(
                private readonly array $map,
                private readonly array $canonicalHosts,
            ) {
            }

            public function findContextByHost(string $normalizedHost): ?array
            {
                if (!isset($this->map[$normalizedHost])) {
                    return null;
                }

                return [
                    'site_id' => $this->map[$normalizedHost]['site_id'],
                    'surface' => $this->map[$normalizedHost]['surface'],
                    'host' => $normalizedHost,
                ];
            }

            public function findCanonicalSiteHost(int $siteId): ?string
            {
                return $this->canonicalHosts[$siteId] ?? null;
            }
        };
    }
}
