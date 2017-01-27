<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\Auth;

use InvalidArgumentException;
use WebHemi\Adapter\Auth\AuthCredentialInterface;
use WebHemi\Auth\Credential\NameAndPasswordCredential;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

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

        $credential->addCredential('username', 'test');
        $expectedResult = ['username' => 'test', 'password' => ''];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $credential->addCredential('password', 'test');
        $expectedResult = ['username' => 'test', 'password' => 'test'];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $credential->addCredential('username', '');
        $expectedResult = ['username' => '', 'password' => 'test'];
        $this->assertArraysAreSimilar($expectedResult, $credential->getCredentials());

        $this->expectException(InvalidArgumentException::class);
        $credential->addCredential('something', 'test');
    }
}
