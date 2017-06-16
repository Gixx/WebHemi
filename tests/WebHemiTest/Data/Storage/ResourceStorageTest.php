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

use WebHemi\DateTime;
use Prophecy\Argument;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ResourceStorageTest.
 */
class ResourceStorageTest extends TestCase
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
        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(ResourceStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_am_resource', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_am_resource', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getConnector());
        $this->assertFalse($defaultAdapterInstance === $storage->getConnector());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(ResourceEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getResourceById method.
     */
    public function testGetResourceById()
    {
        $data = [
            0 => [
                'id_am_resource' => 1,
                'name' => 'test.resource',
                'title' => 'Test Resource',
                'type' => 'route',
                'description' => 'A test resource record',
                'is_read_only' => 0,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    if ($args[0]['id_am_resource'] == 1) {
                        return $data;
                    }

                    return [];
                }
            );

        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getResourceById(3);
        $this->assertEmpty($actualResult);

        /** @var ResourceEntity $actualResult */
        $actualResult = $storage->getResourceById(1);
        $this->assertInstanceOf(ResourceEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data[0]['name'], $actualResult->getName());
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertFalse($actualResult->getReadOnly());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[0], $actualData);
    }

    /**
     * Test the getResourceByName method.
     */
    public function testGetResourceByName()
    {
        $data = [
            0 => [
                'id_am_resource' => 1,
                'name' => 'test.resource',
                'title' => 'Test Resource',
                'type' => 'route',
                'description' => 'A test resource record',
                'is_read_only' => 0,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ]
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('array'))
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

        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getResourceByName('someResource');
        $this->assertEmpty($actualResult);

        /** @var ResourceEntity $actualResult */
        $actualResult = $storage->getResourceByName('test.resource');
        $this->assertInstanceOf(ResourceEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data[0]['name'], $actualResult->getName());
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertFalse($actualResult->getReadOnly());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[0], $actualData);
    }

    /**
     * Test the getResources method.
     */
    public function testGetResources()
    {
        $data = [
            0 => [
                'id_am_resource' => 1,
                'name' => 'test.resource1',
                'title' => 'Test Resource 1',
                'type' => 'route',
                'description' => 'A test resource record',
                'is_read_only' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
            1 => [
                'id_am_resource' => 2,
                'name' => 'test.resource2',
                'title' => 'Test Resource 2',
                'type' => 'route',
                'description' => 'A test resource record',
                'is_read_only' => 0,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
            2 => [
                'id_am_resource' => 3,
                'name' => 'test.resource3',
                'title' => 'Test Resource 3',
                'type' => 'route',
                'description' => 'A test resource record',
                'is_read_only' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ]
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    if (empty($args[0])) {
                        return $data;
                    }
                    return [];
                }
            );

        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        /** @var ResourceEntity[] $actualResult */
        $actualResult = $storage->getResources();
        $this->assertSame(3, count($actualResult));

        $this->assertInstanceOf(ResourceEntity::class, $actualResult[0]);
        $this->assertSame('Test Resource 1', $actualResult[0]->getTitle());
        $this->assertTrue($actualResult[0]->getReadOnly());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[0]]);
        $this->assertArraysAreSimilar($data[0], $actualData);

        $this->assertInstanceOf(ResourceEntity::class, $actualResult[1]);
        $this->assertFalse($actualResult[1]->getReadOnly());
        $this->assertSame('Test Resource 2', $actualResult[1]->getTitle());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[1]]);
        $this->assertArraysAreSimilar($data[1], $actualData);

        $this->assertInstanceOf(ResourceEntity::class, $actualResult[1]);
        $this->assertTrue($actualResult[2]->getReadOnly());
        $this->assertSame('Test Resource 3', $actualResult[2]->getTitle());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[2]]);
        $this->assertArraysAreSimilar($data[2], $actualData);
    }
}
