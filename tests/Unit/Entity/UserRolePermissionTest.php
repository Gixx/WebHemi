<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserRolePermissionTest extends TestCase
{
    #[Test]
    public function normalizesNamesAndEmail(): void
    {
        $user = (new User())
            ->setEmail('  Admin@Example.com ')
            ->setPasswordHash('hashed-password');

        $role = (new Role())
            ->setName(' role_admin ')
            ->setLabel('Administrator');

        $permission = (new Permission())
            ->setName(' Post.Edit ')
            ->setLabel('Edit posts');

        self::assertSame('admin@example.com', $user->getEmail());
        self::assertSame('ROLE_ADMIN', $role->getName());
        self::assertSame('post.edit', $permission->getName());
    }

    #[Test]
    public function linksUserAndRoleBidirectionally(): void
    {
        $user = (new User())
            ->setEmail('editor@example.com')
            ->setPasswordHash('hashed-password');

        $role = (new Role())
            ->setName('ROLE_EDITOR')
            ->setLabel('Editor');

        $user->addRole($role);

        self::assertTrue($user->getRoles()->contains($role));
        self::assertTrue($role->getUserRoles()->contains($user));

        $user->removeRole($role);

        self::assertFalse($user->getRoles()->contains($role));
        self::assertFalse($role->getUserRoles()->contains($user));
    }

    #[Test]
    public function linksRoleAndPermissionBidirectionally(): void
    {
        $role = (new Role())
            ->setName('ROLE_ADMIN')
            ->setLabel('Administrator');

        $permission = (new Permission())
            ->setName('admin.dashboard')
            ->setLabel('Access admin dashboard');

        $role->addPermission($permission);

        self::assertTrue($role->getPermissions()->contains($permission));
        self::assertTrue($permission->getRoles()->contains($role));

        $role->removePermission($permission);

        self::assertFalse($role->getPermissions()->contains($permission));
        self::assertFalse($permission->getRoles()->contains($role));
    }
}
