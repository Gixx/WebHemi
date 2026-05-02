<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Request;

use App\Security\Request\AdminSurfaceRootRequestMatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class AdminSurfaceRootRequestMatcherTest extends TestCase
{
    #[Test]
    public function matchesOnlyAdminSurfaceRoot(): void
    {
        $matcher = new AdminSurfaceRootRequestMatcher();

        $adminRoot = Request::create('/');
        $adminRoot->attributes->set('surface', 'admin');

        $siteRoot = Request::create('/');
        $siteRoot->attributes->set('surface', 'site');

        $adminDashboard = Request::create('/admin');
        $adminDashboard->attributes->set('surface', 'admin');

        self::assertTrue($matcher->matches($adminRoot));
        self::assertFalse($matcher->matches($siteRoot));
        self::assertFalse($matcher->matches($adminDashboard));
    }
}
