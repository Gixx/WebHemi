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
namespace WebHemiTest\Data\Storage;

use Prophecy\Argument;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class UserGroupStorageTest
 */
class UserGroupStorageTest extends TestCase
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
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn($defaultAdapter->reveal());

        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractStorage
     */
    public function testStorageInit()
    {
        $dataEntity = new UserGroupEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserGroupStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(UserGroupStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_user_group', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_user_group', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getConnector());
        $this->assertFalse($defaultAdapterInstance === $storage->getConnector());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(UserGroupEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getUserGroupById method.
     */
    public function testGetUserGroupById()
    {
        $data = [
            [
                'id_user_group' => 1,
                'name' => 'admin',
                'title' => 'Admins',
                'description' => 'Administrator group',
                'is_read_only' => true,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  null,
            ],
            [
                'id_user_group' => 2,
                'name' => 'guest',
                'title' => 'Guests',
                'description' => 'Visitor group',
                'is_read_only' => false,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  null,
            ],
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('int'), Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if (in_array($args[0]['id_user_group'], [1, 2])) {
                        return [$data[($args[0]['id_user_group'] - 1)]];
                    }

                    return [];
                }
            );

        $dataEntity = new UserGroupEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserGroupStorage($defaultAdapterInstance, $dataEntity);


        /** @var UserGroupEntity $actualResult */
        $actualResult = $storage->getUserGroupById(1);
        $this->assertInstanceOf(UserGroupEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertEquals($data[0]['description'], $actualResult->getDescription());
        $this->assertTrue($actualResult->getReadOnly());

        /** @var UserGroupEntity $actualResult */
        $actualResult = $storage->getUserGroupById(2);
        $this->assertInstanceOf(UserGroupEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[1]['title'], $actualResult->getTitle());
        $this->assertEquals($data[1]['description'], $actualResult->getDescription());
        $this->assertFalse($actualResult->getReadOnly());

        $actualResult = $storage->getUserGroupById(3);
        $this->assertEmpty($actualResult);
    }

    /**
     * Test the getUserGroupByName method.
     */
    public function testGetUserGroupByName()
    {
        $data = [
            [
                'id_user_group' => 1,
                'name' => 'admin',
                'title' => 'Admins',
                'description' => 'Administrator group',
                'is_read_only' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
            [
                'id_user_group' => 2,
                'name' => 'guest',
                'title' => 'Guests',
                'description' => 'Visitor group',
                'is_read_only' => 0,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('int'), Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if (isset($args[0]['name'])) {
                        foreach ($data as $itemData) {
                            if ($itemData['name'] == $args[0]['name']) {
                                return [$itemData];
                            }
                        }
                    }

                    return [];
                }
            );

        $dataEntity = new UserGroupEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserGroupStorage($defaultAdapterInstance, $dataEntity);


        /** @var UserGroupEntity $actualResult */
        $actualResult = $storage->getUserGroupByName('admin');
        $this->assertInstanceOf(UserGroupEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertEquals($data[0]['description'], $actualResult->getDescription());
        $this->assertTrue($actualResult->getReadOnly());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[0], $actualData);

        /** @var UserGroupEntity $actualResult */
        $actualResult = $storage->getUserGroupByName('guest');
        $this->assertInstanceOf(UserGroupEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[1]['title'], $actualResult->getTitle());
        $this->assertEquals($data[1]['description'], $actualResult->getDescription());
        $this->assertFalse($actualResult->getReadOnly());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[1], $actualData);

        $actualResult = $storage->getUserGroupByName('someName');
        $this->assertEmpty($actualResult);
    }
}
