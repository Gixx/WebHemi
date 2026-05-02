<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

/** @extends Voter<string, mixed> */
final class PermissionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return '' !== trim($attribute) && str_contains($attribute, '.');
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

        return $user->hasPermission($attribute);
    }
}
