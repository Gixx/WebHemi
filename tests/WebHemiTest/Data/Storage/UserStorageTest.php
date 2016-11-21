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
namespace WebHemiTest\Data\Storage;

use WebHemi\DateTime;
use Prophecy\Argument;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class UserStorageTest.
 */
class UserStorageTest extends TestCase
{
    private $defaultAdapter;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractDataStorage
     */
    public function testStorageInit()
    {
        $dataEntity = new UserEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(UserStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_user', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_user', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(UserEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getUserById() method.
     */
    public function testGetUserById()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('testPassword'),
            'hash' => null,
            'is_active' => true,
            'is_enabled' => true,
            'date_created' =>  '2016-03-24 16:25:12',
            'date_modified' =>  null,
        ];

        $this->defaultAdapter
            ->getData(Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if ($args[0] == 1) {
                        return $data;
                    }

                    return false;
                }
            );

        $dataEntity = new UserEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getUserById(3);
        $this->assertFalse($actualResult);

        /** @var UserEntity $actualResult */
        $actualResult = $storage->getUserById(1);
        $this->assertInstanceOf(UserEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data['password'], $actualResult->getPassword());
        $this->assertSame(true, $actualResult->getEnabled());
    }

    /**
     * Test the getUserByEmail() method.
     */
    public function testGetUserByEmail()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('testPassword'),
            'hash' => null,
            'is_active' => 1,
            'is_enabled' => 1,
            'date_created' =>  '2016-03-24 16:25:12',
            'date_modified' =>  '2016-03-24 16:25:12',
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if ($args[0]['email'] == 'test.address@foo.org') {
                        return [$data];
                    }
                    return false;
                }
            );

        $dataEntity = new UserEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getUserByEmail('wrong.address@foo.org');
        $this->assertFalse($actualResult);

        /** @var UserEntity $actualResult */
        $actualResult = $storage->getUserByEmail('test.address@foo.org');
        $this->assertInstanceOf(UserEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data['password'], $actualResult->getPassword());
        $this->assertSame(true, $actualResult->getEnabled());

        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data, $actualData);
    }

    /**
     * Test the getUserByUserName() method.
     */
    public function testGetUserByUserName()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('testPassword'),
            'hash' => null,
            'is_active' => 1,
            'is_enabled' => 1,
            'date_created' =>  '2016-03-24 16:25:12',
            'date_modified' =>  '2016-03-24 16:25:12',
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if ($args[0]['username'] == 'testUser') {
                        return [$data];
                    }
                    return false;
                }
            );

        $dataEntity = new UserEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getUserByUserName('Donald Trump');
        $this->assertFalse($actualResult);

        /** @var UserEntity $actualResult */
        $actualResult = $storage->getUserByUserName('testUser');
        $this->assertInstanceOf(UserEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data['password'], $actualResult->getPassword());
        $this->assertSame(true, $actualResult->getEnabled());

        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data, $actualData);
    }
}
