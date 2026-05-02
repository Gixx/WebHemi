<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserAvatarTest extends TestCase
{
    #[Test]
    public function defaultAvatarTypeUsesLocalDefaultImage(): void
    {
        $user = (new User())
            ->setEmail('admin@example.com')
            ->setAvatarType(User::AVATAR_TYPE_DEFAULT);

        self::assertSame('/assets/admin/icons/avatar/default-male.svg', $user->getAvatarUrl());
    }

    #[Test]
    public function gravatarTypeBuildsAvatarUrlFromEmailHash(): void
    {
        $user = (new User())
            ->setEmail('Admin@Example.com')
            ->setAvatarType(User::AVATAR_TYPE_GRAVATAR);

        self::assertSame(
            'https://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?d=mp&s=96',
            $user->getAvatarUrl(),
        );
    }

    #[Test]
    public function uploadTypeFallsBackWhenPathIsMissing(): void
    {
        $user = (new User())
            ->setEmail('admin@example.com')
            ->setAvatarType(User::AVATAR_TYPE_UPLOAD);

        self::assertSame('/assets/admin/icons/avatar/default-male.svg', $user->getAvatarUrl());
    }

    #[Test]
    public function uploadTypeReturnsAbsolutePathForRelativeUploadPath(): void
    {
        $user = (new User())
            ->setEmail('admin@example.com')
            ->setAvatarType(User::AVATAR_TYPE_UPLOAD)
            ->setAvatarPath('uploads/avatars/admin.png');

        self::assertSame('/uploads/avatars/admin.png', $user->getAvatarUrl());
    }
}
