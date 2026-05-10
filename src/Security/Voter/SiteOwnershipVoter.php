<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Checks that the site ID passed as subject matches the current request's site context.
 * Use as: #[IsGranted('site.own', subject: 'routeParamName')]
 *
 * @extends Voter<'site.own', int>
 */
final class SiteOwnershipVoter extends Voter
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'site.own' && is_int($subject);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        if (in_array('ROLE_ADMIN', $token->getRoleNames(), true)) {
            return true;
        }

        $currentSiteId = $this->requestStack->getCurrentRequest()?->attributes->getInt('site_id') ?? 0;

        return $currentSiteId > 0 && $subject === $currentSiteId;
    }
}
