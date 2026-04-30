<?php

namespace App\Tests\Unit\Model;

use App\Model\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function userCreation(): void
    {
        $user = new User(1, 'John Doe', 'some.email@foo.org');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->getId());
    }
}
