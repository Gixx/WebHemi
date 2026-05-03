<?php

declare(strict_types=1);

namespace App\SiteHost\Verification;

final readonly class HostVerificationResult
{
    public function __construct(
        public bool $verified,
        public string|null $checkedUrl = null,
    ) {
    }
}
