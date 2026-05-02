<?php

declare(strict_types=1);

namespace App\Routing\Request;

use App\Routing\HostContextResolver;
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

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 250],
        ];
    }
}
