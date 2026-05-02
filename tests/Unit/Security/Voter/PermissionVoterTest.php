<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Security\Voter\PermissionVoter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class PermissionVoterTest extends TestCase
{
    #[Test]
    public function grantsAccessWhenTheUserHasThePermission(): void
    {
        $user = (new User())
            ->setEmail('editor@example.com')
            ->setPasswordHash('hashed-password');

        $role = (new Role())
            ->setName('ROLE_EDITOR')
            ->setLabel('Editor');

        $permission = (new Permission())
            ->setName('user.edit')
            ->setLabel('Edit users');

        $role->addPermission($permission);
        $user->addRole($role);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $voter = new PermissionVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, ['user.edit']));
    }

    #[Test]
    public function deniesAccessWhenThePermissionIsMissing(): void
    {
        $user = (new User())
            ->setEmail('viewer@example.com')
            ->setPasswordHash('hashed-password');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $voter = new PermissionVoter();

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, null, ['user.delete']));
    }

    #[Test]
    public function grantsAccessForRoleAdminWithoutMappedPermissions(): void
    {
        $user = (new User())
            ->setEmail('admin@example.com')
            ->setPasswordHash('hashed-password');

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_ADMIN']);

        $voter = new PermissionVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, ['permission.delete']));
    }

    #[Test]
    public function abstainsForNonPermissionAttributes(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $voter = new PermissionVoter();

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, null, ['ROLE_ADMIN']));
    }
}
