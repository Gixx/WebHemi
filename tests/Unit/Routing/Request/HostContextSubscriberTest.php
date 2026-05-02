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
        ])));

        $request = Request::create('http://admin.mysite.local/');
        $event = new RequestEvent($this->kernelStub(), $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelRequest($event);

        self::assertSame(1, $request->attributes->get('site_id'));
        self::assertSame('admin', $request->attributes->get('surface'));
        self::assertSame('admin.mysite.local', $request->attributes->get('resolved_host'));
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
