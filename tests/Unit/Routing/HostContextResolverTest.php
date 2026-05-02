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

    /**
     * @param array<string, array{site_id:int, surface:string}> $map
     */
    private function provider(array $map): HostContextProviderInterface
    {
        return new class ($map) implements HostContextProviderInterface {
            /**
             * @param array<string, array{site_id:int, surface:string}> $map
             */
            public function __construct(private readonly array $map)
            {
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
        };
    }
}
