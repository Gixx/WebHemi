<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, mixed> */
final class PermissionVoter extends Voter
{
    public function __construct(
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return '' !== trim($attribute) && str_contains($attribute, '.') && $attribute !== 'site.own';
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

        $siteId = match (true) {
            is_int($subject) && $subject > 0                              => $subject,
            is_string($subject) && ctype_digit($subject) && $subject > '0' => (int) $subject,
            default                                                        => 0,
        };

        if ($siteId > 0) {
            $assignment = $this->siteAssignmentRepository->findForUserAndSite($user, $siteId);
            if (!$assignment instanceof SiteAssignment) {
                return false;
            }
            if ($assignment->getRole()->getName() === 'ROLE_SITE_ADMIN') {
                return true;
            }

            return $assignment->getRole()->hasPermission($attribute);
        }

        foreach ($this->siteAssignmentRepository->findBy(['user' => $user]) as $assignment) {
            if ($assignment->getRole()->getName() === 'ROLE_SITE_ADMIN') {
                return true;
            }
            if ($assignment->getRole()->hasPermission($attribute)) {
                return true;
            }
        }

        return false;
    }
}
