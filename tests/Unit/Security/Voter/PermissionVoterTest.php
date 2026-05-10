<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\Site;
use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
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
        $role = (new Role())->setName('ROLE_EDITOR')->setLabel('Editor');
        $permission = (new Permission())->setName('user.edit')->setLabel('Edit users');
        $role->addPermission($permission);

        $user = (new User())->setEmail('editor@example.com')->setPasswordHash('hashed');
        $site = (new Site())->setSlug('mysite')->setName('My Site');

        $assignment = (new SiteAssignment())->setUser($user)->setSite($site)->setRole($role);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $voter = new PermissionVoter($this->repositoryReturning($user, 1, $assignment));

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, 1, ['user.edit']));
    }

    #[Test]
    public function deniesAccessWhenThePermissionIsMissing(): void
    {
        $role = (new Role())->setName('ROLE_EDITOR')->setLabel('Editor');
        $user = (new User())->setEmail('viewer@example.com')->setPasswordHash('hashed');
        $site = (new Site())->setSlug('mysite')->setName('My Site');

        $assignment = (new SiteAssignment())->setUser($user)->setSite($site)->setRole($role);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $voter = new PermissionVoter($this->repositoryReturning($user, 1, $assignment));

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, 1, ['user.delete']));
    }

    #[Test]
    public function deniesAccessWhenThereIsNoSiteAssignment(): void
    {
        $user = (new User())->setEmail('nobody@example.com')->setPasswordHash('hashed');

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $voter = new PermissionVoter($this->repositoryReturning($user, 1, null));

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, 1, ['site.edit']));
    }

    #[Test]
    public function deniesAccessWhenThereAreNoAssignmentsWithoutSiteSubject(): void
    {
        $user = (new User())->setEmail('nobody@example.com')->setPasswordHash('hashed');

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $repo = $this->createMock(SiteAssignmentRepository::class);
        $repo->expects(self::once())
            ->method('findBy')
            ->with(['user' => $user])
            ->willReturn([]);

        $voter = new PermissionVoter($repo);

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, null, ['site.edit']));
    }

    #[Test]
    public function grantsAccessForRoleSiteAdmin(): void
    {
        $role = (new Role())->setName('ROLE_SITE_ADMIN')->setLabel('Site Administrator');
        $user = (new User())->setEmail('siteadmin@example.com')->setPasswordHash('hashed');
        $site = (new Site())->setSlug('mysite')->setName('My Site');

        $assignment = (new SiteAssignment())->setUser($user)->setSite($site)->setRole($role);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $voter = new PermissionVoter($this->repositoryReturning($user, 1, $assignment));

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, 1, ['site.delete']));
    }

    #[Test]
    public function grantsAccessForRoleAdmin(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        $token->method('getRoleNames')->willReturn(['ROLE_ADMIN']);

        $repo = $this->createMock(SiteAssignmentRepository::class);
        $repo->expects(self::never())->method('findForUserAndSite');
        $repo->expects(self::never())->method('findBy');

        $voter = new PermissionVoter($repo);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, ['permission.delete']));
    }

    #[Test]
    public function abstainsForNonPermissionAttributes(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        $token->method('getRoleNames')->willReturn([]);

        $repo = $this->createMock(SiteAssignmentRepository::class);
        $repo->expects(self::never())->method('findForUserAndSite');
        $repo->expects(self::never())->method('findBy');

        $voter = new PermissionVoter($repo);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, null, ['ROLE_ADMIN']));
    }

    private function repositoryReturning(User $user, int $siteId, ?SiteAssignment $result): SiteAssignmentRepository
    {
        $repo = $this->createMock(SiteAssignmentRepository::class);
        $repo->expects(self::once())
            ->method('findForUserAndSite')
            ->with($user, $siteId)
            ->willReturn($result);

        return $repo;
    }
}
