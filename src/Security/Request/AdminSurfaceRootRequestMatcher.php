<?php

declare(strict_types=1);

namespace App\Security\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class AdminSurfaceRootRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request): bool
    {
        if ('/' !== $request->getPathInfo()) {
            return false;
        }

        return 'admin' === $request->attributes->get('surface');
    }
}
