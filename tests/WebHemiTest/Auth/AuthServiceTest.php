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

use Prophecy\Argument;
use WebHemi\Auth\ServiceInterface as AuthAdapterInterface;
use WebHemi\Auth\StorageInterface as AuthStorageInterface;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Query\QueryInterface as DataAdapterInterface;
use WebHemi\Auth\Result\Result;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Data\Storage\StorageInterface as DataStorageInterface;
use WebHemiTest\TestService\EmptyAuthAdapter;
use WebHemiTest\TestService\EmptyAuthStorage;
use WebHemiTest\TestService\EmptyCredential;
use WebHemiTest\TestService\EmptyEntity;
use WebHemiTest\TestService\EmptyUserStorage;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthServiceTest
 */
class AuthServiceTest extends TestCase
{
    /** @var array */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

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
     * Tests class constructor.
     */
    public function testConstructor()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new EmptyEntity();
        $entitySet = new EntitySet();
        $dataStorage = new EmptyUserStorage($defaultAdapterInstance, $entitySet, $dataEntity);

        $adapter = new EmptyAuthAdapter(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $this->assertInstanceOf(AuthAdapterInterface::class, $adapter);
        $actualObject = $this->invokePrivateMethod($adapter, 'getAuthStorage', []);
        $this->assertInstanceOf(AuthStorageInterface::class, $actualObject);
        $actualObject = $this->invokePrivateMethod($adapter, 'getDataStorage', []);
        $this->assertInstanceOf(DataStorageInterface::class, $actualObject);
        $actualObject = $this->invokePrivateMethod($adapter, 'getNewAuthResultInstance', []);
        $this->assertInstanceOf(Result::class, $actualObject);
        $this->assertFalse($result === $actualObject);
    }

    /**
     * Tests authentication.
     */
    public function testAuthenticate()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $entitySet = new EntitySet();
        $dataStorage = new EmptyUserStorage($defaultAdapterInstance, $entitySet, $dataEntity);

        $adapter = new EmptyAuthAdapter(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $this->assertFalse($adapter->hasIdentity());
        $this->assertNull($adapter->getIdentity());

        $emptyCredential = new EmptyCredential();

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE_OTHER);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertSame(Result::FAILURE_OTHER, $result->getCode());
        $this->assertNull($adapter->getIdentity());
        $this->assertNotEmpty($result->getMessage());

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE_CREDENTIAL_INVALID);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertSame(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertNull($adapter->getIdentity());
        $this->assertNotEmpty($result->getMessage());

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE_IDENTITY_NOT_FOUND);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertSame(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertNull($adapter->getIdentity());
        $this->assertNotEmpty($result->getMessage());

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertSame(Result::FAILURE, $result->getCode());
        $this->assertNull($adapter->getIdentity());
        $this->assertNotEmpty($result->getMessage());

        $emptyCredential->setCredential('authResultShouldBe', Result::SUCCESS);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertSame(Result::SUCCESS, $result->getCode());
        $this->assertInstanceOf(UserEntity::class, $adapter->getIdentity());
        $this->assertSame('test', $adapter->getIdentity()->getUserName());

        $adapter->clearIdentity();
        $this->assertNull($adapter->getIdentity());
    }

    /**
     * Tests setIdentity() method.
     */
    public function testSetIdentity()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $dataEntity->setUserName('new entity');
        $entitySet = new EntitySet();
        $dataStorage = new EmptyUserStorage($defaultAdapterInstance, $entitySet, $dataEntity);

        $adapter = new EmptyAuthAdapter(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $this->assertFalse($adapter->hasIdentity());
        $this->assertNull($adapter->getIdentity());

        $adapter->setIdentity($dataEntity);
        $this->assertInstanceOf(UserEntity::class, $adapter->getIdentity());
        $this->assertTrue($dataEntity === $adapter->getIdentity());
        $this->assertSame('new entity', $adapter->getIdentity()->getUserName());
    }

    /**
     * Tests auth adapter Result
     *
     * @covers \WebHemi\Auth\Result\Result
     */
    public function testResult()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $entitySet = new EntitySet();
        $dataStorage = new EmptyUserStorage($defaultAdapterInstance, $entitySet, $dataEntity);

        $adapter = new EmptyAuthAdapter(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $emptyCredential = new EmptyCredential();

        $emptyCredential->setCredential('authResultShouldBe', Result::SUCCESS);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertTrue($result->isValid());
        $this->assertSame(Result::SUCCESS, $result->getCode());

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE, $result->getCode());

        // set it to a non-valid result code
        $emptyCredential->setCredential('authResultShouldBe', -100);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_OTHER, $result->getCode());
    }
}
