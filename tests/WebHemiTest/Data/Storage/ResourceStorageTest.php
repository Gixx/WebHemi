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

use DateTime;
use Prophecy\Argument;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ResourceStorageTest.
 */
class ResourceStorageTest extends TestCase
{
    private $defaultAdapter;

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
        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(ResourceStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_am_resource', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_am_resource', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

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
            'id_am_resource' => 1,
            'name' => 'test.resource',
            'title' => 'Test Resource',
            'description' => 'A test resource record',
            'is_read_only' => false,
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

        $dataEntity = new ResourceEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ResourceStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getResourceById(3);
        $this->assertFalse($actualResult);

        /** @var ResourceEntity $actualResult */
        $actualResult = $storage->getResourceById(1);
        $this->assertInstanceOf(ResourceEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data['name'], $actualResult->getName());
        $this->assertEquals($data['title'], $actualResult->getTitle());
        $this->assertFalse($actualResult->getReadOnly());
    }
}
