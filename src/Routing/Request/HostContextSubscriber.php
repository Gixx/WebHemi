<?php

declare(strict_types=1);

namespace App\Routing\Request;

use App\Routing\HostContextResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class HostContextSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HostContextResolver $hostContextResolver,
        private string $adminPathPrefix = '/admin',
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $context = $this->hostContextResolver->resolveFromHost($request->getHost());

        if (null === $context) {
            throw new NotFoundHttpException('Unknown host.');
        }

        if ('admin' === $context['surface']) {
            $canonicalHost = $this->hostContextResolver->resolveCanonicalSiteHost((int) $context['site_id']);
            if (is_string($canonicalHost) && '' !== $canonicalHost && $canonicalHost !== $context['host']) {
                $event->setResponse(new RedirectResponse($this->buildCanonicalAdminUrl($request, $canonicalHost)));

                return;
            }
        }

        $surface = $context['surface'];
        if ('site' === $surface && $this->isAdminPath($request->getPathInfo())) {
            $surface = 'admin';
        }

        $request->attributes->set('site_id', $context['site_id']);
        $request->attributes->set('surface', $surface);
        $request->attributes->set('resolved_host', $context['host']);
    }

    private function isAdminPath(string $pathInfo): bool
    {
        $prefix = trim($this->adminPathPrefix);
        if ('' === $prefix || '/' === $prefix) {
            return false;
        }

        $normalizedPrefix = '/' . ltrim($prefix, '/');

        return 1 === preg_match('#^' . preg_quote($normalizedPrefix, '#') . '(?:/|$)#', $pathInfo);
    }

    private function buildCanonicalAdminUrl(Request $request, string $canonicalHost): string
    {
        $path = $request->getPathInfo();
        $adminPath = $this->normalizeAdminPath($path);
        $queryString = $request->getQueryString();

        $url = sprintf('%s://%s%s%s', $request->getScheme(), $canonicalHost, $this->formatPort($request), $adminPath);

        if (is_string($queryString) && '' !== $queryString) {
            $url .= '?' . $queryString;
        }

        return $url;
    }

    private function normalizeAdminPath(string $pathInfo): string
    {
        $prefix = '/' . ltrim(trim($this->adminPathPrefix), '/');
        if ('/' === $pathInfo || '' === $pathInfo) {
            return $prefix;
        }

        if ($this->isAdminPath($pathInfo)) {
            return $pathInfo;
        }

        return rtrim($prefix, '/') . '/' . ltrim($pathInfo, '/');
    }

    private function formatPort(Request $request): string
    {
        $port = $request->getPort();
        if (($request->isSecure() && 443 === $port) || (!$request->isSecure() && 80 === $port)) {
            return '';
        }

        return ':' . $port;
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 250],
        ];
    }
}
