<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Application;

use Exception;
use RuntimeException;
use WebHemi\DateTime;
use WebHemi\Application\SessionManager;
use WebHemi\Config\Config;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class SessionManagerTest
 */
class SessionManagerTest extends TestCase
{
    /** @var Config */
    private $config;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../Fixtures/test_config.php';
        $this->config = new Config($config);
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $sessionManager = new SessionManager($this->config);
        $this->assertAttributeEmpty('data', $sessionManager);
        $this->assertAttributeEquals($this->config->getData('session/namespace'), 'namespace', $sessionManager);
        $this->assertAttributeEquals($this->config->getData('session/cookie_prefix'), 'cookiePrefix', $sessionManager);
        $this->assertAttributeEquals(
            $this->config->getData('session/session_name_salt'),
            'sessionNameSalt',
            $sessionManager
        );
    }

    /**
     * Test start() method.
     */
    public function testSessionStart()
    {
        $config = require __DIR__ . '/../Fixtures/test_config.php';
        // Change the namespace, so the sessionStarted() method will return false.
        $config['session']['namespace'] = 'UNITTEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);

        $actualObject = $sessionManager->start('test');
        $this->assertInstanceOf(SessionManager::class, $actualObject);
        $this->assertTrue($actualObject === $sessionManager);

        $config['session']['namespace'] = 'TEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);
        try {
            $sessionManager->start('test');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1000, $e->getCode());
        }
    }

    /**
     * Test regenerateId() method.
     */
    public function testRegenerateId()
    {
        $sessionManager = new SessionManager($this->config);

        $actualObject = $sessionManager->regenerateId();
        $this->assertInstanceOf(SessionManager::class, $actualObject);
        $this->assertTrue($actualObject === $sessionManager);

        $config = require __DIR__ . '/../Fixtures/test_config.php';
        $config['session']['namespace'] = 'UNITTEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);
        try {
            $sessionManager->regenerateId();
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1001, $e->getCode());
        }
    }

    /**
     * Tests set() method
     */
    public function testSetter()
    {
        $sessionManager = new SessionManager($this->config);

        $name = 'test';
        $value = 'value';
        $readOnly = false;

        // set value
        $actualObject = $sessionManager->set($name, $value, $readOnly);
        $this->assertInstanceOf(SessionManager::class, $actualObject);
        $this->assertTrue($actualObject === $sessionManager);
        $this->assertSame($value, $sessionManager->get($name));

        // change value
        $newValue = 'some other value';
        $sessionManager->set($name, $newValue, $readOnly);
        $this->assertSame($newValue, $sessionManager->get($name));

        // change and lock value
        $anotherNewValue = 'yet another value';
        $readOnly = true;
        $sessionManager->set($name, $anotherNewValue, $readOnly);
        $this->assertSame($anotherNewValue, $sessionManager->get($name));

        // try to change readonly data
        try {
            $sessionManager->set($name, 'will not work.');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1006, $e->getCode());
        }

        // unlock and change readonly data
        $sessionManager->unlock($name);
        $sessionManager->set($name, $value);
        $this->assertSame($value, $sessionManager->get($name));

        $config = require __DIR__ . '/../Fixtures/test_config.php';
        $config['session']['namespace'] = 'UNITTEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);
        try {
            $sessionManager->set('some', 'value');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1005, $e->getCode());
        }
    }

    /**
     * Tests get() method.
     */
    public function testGetter()
    {
        $sessionManager = new SessionManager($this->config);

        $name = 'test';
        $value = 'value';
        $sessionManager->set($name, $value);
        $this->assertSame($value, $sessionManager->get($name));

        // return NULL when non exists
        $skipMissing = true;
        $this->assertNull($sessionManager->get('something non existing', $skipMissing));

        // get exception otherwise
        $skipMissing = false;
        try {
            $this->assertNull($sessionManager->get('something non existing', $skipMissing));
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1004, $e->getCode());
        }

        $config = require __DIR__ . '/../Fixtures/test_config.php';
        $config['session']['namespace'] = 'UNITTEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);
        try {
            $sessionManager->get('something');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1003, $e->getCode());
        }
    }

    /**
     * Tests delete() method.
     */
    public function testDelete()
    {
        $sessionManager = new SessionManager($this->config);

        $name = 'test';
        $value = 'value';

        // delete existing non-readonly
        $sessionManager->set($name, $value, false);
        $this->assertSame($value, $sessionManager->get($name));
        $sessionManager->delete($name);
        $this->assertNull($sessionManager->get($name, true));

        // delete existing readonly, no force
        $sessionManager->set($name, $value, true);
        $this->assertSame($value, $sessionManager->get($name));
        try {
            $sessionManager->delete($name);
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1008, $e->getCode());
        }

        // delete existing readonly, force
        $sessionManager->delete($name, true);
        $this->assertNull($sessionManager->get($name, true));

        // delete non-existing
        $sessionManager->delete('something');

        $config = require __DIR__ . '/../Fixtures/test_config.php';
        $config['session']['namespace'] = 'UNITTEST';
        $this->config = new Config($config);
        $sessionManager = new SessionManager($this->config);
        try {
            $sessionManager->delete('something');
        } catch (Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame(1007, $e->getCode());
        }
    }

    /**
     * Tests toArray() method
     */
    public function testToArray()
    {
        $expectedData = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => [
                'complex' => 'data',
                'date' => new DateTime()
            ]
        ];
        $sessionManager = new SessionManager($this->config);
        foreach ($expectedData as $key => $value) {
            $sessionManager->set($key, $value);
        }

        $this->assertArraysAreSimilar($expectedData, $sessionManager->toArray());
    }
}
