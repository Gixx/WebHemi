<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\Auth;

use WebHemi\Session\ServiceAdapter\Base\ServiceAdapter as SessionManager;
use WebHemi\Auth\Storage\Session as SessionStorage;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Entity\UserEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionStorageTest
 */
class SessionStorageTest extends TestCase
{
    /** @var array */
    private $config;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->config = require __DIR__ . '/../test_config.php';
    }

    /**
     * Test storage
     */
    public function testStorage()
    {
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config);
        $userEntity = new UserEntity();

        $storage = new SessionStorage($sessionManager);
        $this->assertFalse($storage->hasIdentity());
        $this->assertEmpty($storage->getIdentity());

        $storage->setIdentity($userEntity);
        $this->assertTrue($storage->hasIdentity());
        $this->assertTrue($userEntity === $storage->getIdentity());

        $storage->clearIdentity();
        $this->assertEmpty($storage->getIdentity());
    }
}
