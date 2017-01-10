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
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ApplicationStorageTest.
 */
class ApplicationStorageTest extends TestCase
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
        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(ApplicationStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_application', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_application', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(ApplicationEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getApplicationById method.
     */
    public function testGetApplicationById()
    {
        $data = [
            'id_application' => 1,
            'name' => 'test.application',
            'title' => 'Test Application',
            'description' => 'A test application record',
            'is_read_only' => 1,
            'date_created' =>  '2016-03-24 16:25:12',
            'date_modified' =>  '2016-03-24 16:25:12',
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

        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getApplicationById(3);
        $this->assertNull($actualResult);

        /** @var ApplicationEntity $actualResult */
        $actualResult = $storage->getApplicationById(1);
        $this->assertInstanceOf(ApplicationEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data['name'], $actualResult->getName());
        $this->assertEquals($data['title'], $actualResult->getTitle());
        $this->assertTrue($actualResult->getReadOnly());

        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data, $actualData);
    }

    /**
     * Test the getApplicationByName method.
     */
    public function testGetApplicationByName()
    {
        $data = [
            0 => [
                'id_application' => 1,
                'name' => 'test.application',
                'title' => 'Test Application',
                'description' => 'A test application record',
                'is_read_only' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ]
        ];


        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if (isset($args[0]['name'])) {
                        foreach ($data as $itemData) {
                            if ($itemData['name'] == $args[0]['name']) {
                                return [$itemData];
                            }
                        }
                    }

                    return false;
                }
            );

        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getApplicationByName('someApplication');
        $this->assertNull($actualResult);

        /** @var ApplicationEntity $actualResult */
        $actualResult = $storage->getApplicationByName('test.application');
        $this->assertInstanceOf(ApplicationEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data[0]['name'], $actualResult->getName());
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertTrue($actualResult->getReadOnly());

        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[0], $actualData);
    }
}
