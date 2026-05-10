<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Checks that the current user has any SiteAssignment for the given site ID.
 * Use as: #[IsGranted('site.own', subject: 'routeParamName')]
 *
 * @extends Voter<'site.own', int>
 */
final class SiteOwnershipVoter extends Voter
{
    public function __construct(
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $siteId = is_int($subject) ? $subject : (is_string($subject) && ctype_digit($subject) ? (int) $subject : 0);

        return $attribute === 'site.own' && $siteId > 0;
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

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->siteAssignmentRepository
                ->findForUserAndSite($user, (int) $subject) instanceof \App\Entity\SiteAssignment;
    }
}
