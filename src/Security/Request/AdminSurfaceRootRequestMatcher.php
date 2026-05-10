<?php

declare(strict_types=1);

namespace App\Security\Request;

use App\Entity\SurfaceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class AdminSurfaceRootRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request): bool
    {
        if ('/' !== $request->getPathInfo()) {
            return false;
        }

        return SurfaceType::tryFrom((string) $request->attributes->get('surface')) === SurfaceType::Admin;
    }
}
