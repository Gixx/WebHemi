<?php

declare(strict_types=1);

namespace App\Tests\Unit\SiteHost\Verification;

use App\SiteHost\Verification\HostOwnershipVerifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class HostOwnershipVerifierTest extends TestCase
{
    #[Test]
    public function buildsDefaultHttpAndHttpsUrlsWhenThereIsNoCurrentRequest(): void
    {
        $verifier = new HostOwnershipVerifier(__DIR__, new RequestStack());

        self::assertSame([
            'http://tenant.mysite.local/token.txt',
            'https://tenant.mysite.local/token.txt',
        ], $this->buildVerificationUrls($verifier));
    }

    #[Test]
    public function prioritizesCurrentHttpRequestPortAndFallsBackToDefaultHttps(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('http://admin.mysite.local:8000/admin/sites/1/hosts/create'));

        $verifier = new HostOwnershipVerifier(__DIR__, $requestStack);

        self::assertSame([
            'http://tenant.mysite.local:8000/token.txt',
            'https://tenant.mysite.local/token.txt',
        ], $this->buildVerificationUrls($verifier));
    }

    #[Test]
    public function prioritizesCurrentHttpsRequestPortAndFallsBackToDefaultHttp(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://admin.mysite.local:8443/admin/sites/1/hosts/create'));

        $verifier = new HostOwnershipVerifier(__DIR__, $requestStack);

        self::assertSame([
            'https://tenant.mysite.local:8443/token.txt',
            'http://tenant.mysite.local/token.txt',
        ], $this->buildVerificationUrls($verifier));
    }

    #[Test]
    public function omitsTheDefaultHttpPortFromThePreferredCandidate(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('http://admin.mysite.local/admin/sites/1/hosts/create'));

        $verifier = new HostOwnershipVerifier(__DIR__, $requestStack);

        self::assertSame([
            'http://tenant.mysite.local/token.txt',
            'https://tenant.mysite.local/token.txt',
        ], $this->buildVerificationUrls($verifier));
    }

    #[Test]
    public function omitsTheDefaultHttpsPortFromThePreferredCandidate(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://admin.mysite.local/admin/sites/1/hosts/create'));

        $verifier = new HostOwnershipVerifier(__DIR__, $requestStack);

        self::assertSame([
            'https://tenant.mysite.local/token.txt',
            'http://tenant.mysite.local/token.txt',
        ], $this->buildVerificationUrls($verifier));
    }

    /**
     * @return list<string>
     */
    private function buildVerificationUrls(HostOwnershipVerifier $verifier): array
    {
        $method = new \ReflectionMethod($verifier, 'buildVerificationUrls');

        return $method->invoke($verifier, 'tenant.mysite.local', 'token.txt');
    }
}
