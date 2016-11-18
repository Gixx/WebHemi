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
    /** @var array */
    private $config;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->config = require __DIR__ . '/../Fixtures/test_config.php';
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
        $this->assertAttributeEmpty('data', $sessionManager);
        $this->assertAttributeEquals($config->getData('session/namespace'), 'namespace', $sessionManager);
        $this->assertAttributeEquals($config->getData('session/cookie_prefix'), 'cookiePrefix', $sessionManager);
        $this->assertAttributeEquals($config->getData('session/session_name_salt'), 'sessionNameSalt', $sessionManager);
    }

    /**
     * Test start() method.
     */
    public function testSessionStart()
    {
        // Change the namespace, so the sessionStarted() method will return false.
        $this->config['session']['namespace'] = 'UNITTEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));

        $actualObject = $sessionManager->start('test');
        $this->assertInstanceOf(SessionManager::class, $actualObject);
        $this->assertTrue($actualObject === $sessionManager);

        $this->config['session']['namespace'] = 'TEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
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
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));

        $actualObject = $sessionManager->regenerateId();
        $this->assertInstanceOf(SessionManager::class, $actualObject);
        $this->assertTrue($actualObject === $sessionManager);

        $this->config['session']['namespace'] = 'UNITTEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
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
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));

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

        $this->config['session']['namespace'] = 'UNITTEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
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
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));

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

        $this->config['session']['namespace'] = 'UNITTEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
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
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));

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

        $this->config['session']['namespace'] = 'UNITTEST';
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
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
        $config = new Config($this->config);
        $sessionManager = new SessionManager($config->getConfig('session'));
        foreach ($expectedData as $key => $value) {
            $sessionManager->set($key, $value);
        }

        $this->assertArraysAreSimilar($expectedData, $sessionManager->toArray());
    }
}
