<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\Auth;

use InvalidArgumentException;
use WebHemi\Auth\CredentialInterface as AuthCredentialInterface;
use WebHemi\Auth\Credential\NameAndPasswordCredential;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class NameAndPasswordCredentialTest
 */
class NameAndPasswordCredentialTest extends TestCase
{
    use AssertTrait;

    /**
     * Tests constructor.
     */
    public function testConstruct()
    {
        $credential = new NameAndPasswordCredential();

        $this->assertInstanceOf(AuthCredentialInterface::class, $credential);
    }

    /**
     * Tests setter and getter methods.
     */
    public function testSetterGetter()
    {
        $credential = new NameAndPasswordCredential();

        $expectedResult = ['username' => '', 'password' => ''];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $credential->setCredential('username', 'test');
        $expectedResult = ['username' => 'test', 'password' => ''];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $credential->setCredential('password', 'test');
        $expectedResult = ['username' => 'test', 'password' => 'test'];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $credential->setCredential('username', '');
        $expectedResult = ['username' => '', 'password' => 'test'];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $this->expectException(InvalidArgumentException::class);
        $credential->setCredential('something', 'test');
    }
}
