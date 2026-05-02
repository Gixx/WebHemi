<?php

declare(strict_types=1);

namespace App\Tests\Unit\Routing\Request;

use App\Routing\HostContextProviderInterface;
use App\Routing\HostContextResolver;
use App\Routing\Request\HostContextSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class HostContextSubscriberTest extends TestCase
{
    #[Test]
    public function attachesResolvedContextAttributes(): void
    {
        $subscriber = new HostContextSubscriber(new HostContextResolver($this->provider([
            'admin.mysite.local' => ['site_id' => 1, 'surface' => 'admin'],
        ], [
            1 => 'mysite.local',
        ])));

        $request = Request::create('http://admin.mysite.local/');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelRequest($event);

        self::assertSame('http://mysite.local/admin', $event->getResponse()?->headers->get('Location'));
    }

    #[Test]
    public function redirectsAdminAliasPathRequestsToCanonicalAdminPath(): void
    {
        $subscriber = new HostContextSubscriber(new HostContextResolver($this->provider([
            'admin.mysite.local' => ['site_id' => 1, 'surface' => 'admin'],
        ], [
            1 => 'mysite.local',
        ])));

        $request = Request::create('http://admin.mysite.local:8000/sites?page=2');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelRequest($event);

        self::assertSame('http://mysite.local:8000/admin/sites?page=2', $event->getResponse()?->headers->get('Location'));
    }

    #[Test]
    public function throwsOnUnknownHost(): void
    {
        $subscriber = new HostContextSubscriber(new HostContextResolver($this->provider([])));

        $request = Request::create('http://unknown.mysite.local/');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(NotFoundHttpException::class);

        $subscriber->onKernelRequest($event);
    }

    #[Test]
    public function resolvesAdminSurfaceForAdminPathOnSiteHost(): void
    {
        $subscriber = new HostContextSubscriber(
            new HostContextResolver($this->provider([
                'mysite.local' => ['site_id' => 1, 'surface' => 'site'],
            ])),
            '/admin',
        );

        $request = Request::create('http://mysite.local/admin/posts');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelRequest($event);

        self::assertSame('admin', $request->attributes->get('surface'));
        self::assertSame(1, $request->attributes->get('site_id'));
    }

    #[Test]
    public function doesNotResolveAdminSurfaceForSimilarPathPrefix(): void
    {
        $subscriber = new HostContextSubscriber(
            new HostContextResolver($this->provider([
                'mysite.local' => ['site_id' => 1, 'surface' => 'site'],
            ])),
            '/admin',
        );

        $request = Request::create('http://mysite.local/administrator');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelRequest($event);

        self::assertSame('site', $request->attributes->get('surface'));
    }

    private function kernelStub(): HttpKernelInterface
    {
        return new class () implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response();
            }
        };
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
