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
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ApplicationStorageTest.
 */
class ApplicationStorageTest extends TestCase
{
    /** @var DataAdapterInterface */
    private $defaultAdapter;
    /** @var array */
    private $data;

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

        $this->data =  [
            0 => [
                'id_application' => 1,
                'name' => 'test.application',
                'title' => 'Test Application',
                'description' => 'A test application record',
                'is_read_only' => 1,
                'is_enabled' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
            1 => [
                'id_application' => 2,
                'name' => 'test.application.2',
                'title' => 'Test Application 2',
                'description' => 'Another test application record',
                'is_read_only' => 0,
                'is_enabled' => 1,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ],
            2 => [
                'id_application' => 3,
                'name' => 'test.application.3',
                'title' => 'Test Application 3',
                'description' => 'The last test application record',
                'is_read_only' => 0,
                'is_enabled' => 0,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  '2016-03-24 16:25:12',
            ]
        ];

        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractStorage
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
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getConnector());
        $this->assertFalse($defaultAdapterInstance === $storage->getConnector());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(ApplicationEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getApplications method.
     */
    public function testGetApplications()
    {
        $data = $this->data;

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

        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        /** @var ApplicationEntity[] $actualResult */
        $actualResult = $storage->getApplications();
        $this->assertSame(3, count($actualResult));

        $this->assertInstanceOf(ApplicationEntity::class, $actualResult[0]);
        $this->assertSame('Test Application', $actualResult[0]->getTitle());
        $this->assertTrue($actualResult[0]->getReadOnly());
        $this->assertTrue($actualResult[0]->getEnabled());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[0]]);
        $this->assertArraysAreSimilar($data[0], $actualData);

        $this->assertInstanceOf(ApplicationEntity::class, $actualResult[1]);
        $this->assertFalse($actualResult[1]->getReadOnly());
        $this->assertTrue($actualResult[1]->getEnabled());
        $this->assertSame('Test Application 2', $actualResult[1]->getTitle());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[1]]);
        $this->assertArraysAreSimilar($data[1], $actualData);

        $this->assertInstanceOf(ApplicationEntity::class, $actualResult[1]);
        $this->assertFalse($actualResult[2]->getReadOnly());
        $this->assertFalse($actualResult[2]->getEnabled());
        $this->assertSame('Test Application 3', $actualResult[2]->getTitle());
        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult[2]]);
        $this->assertArraysAreSimilar($data[2], $actualData);
    }

    /**
     * Test the getApplicationById method.
     */
    public function testGetApplicationById()
    {
        $data = $this->data;

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'), Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    if (isset($args[0]['id_application'])) {
                        foreach ($data as $itemData) {
                            if ($itemData['id_application'] == $args[0]['id_application']) {
                                return [$itemData];
                            }
                        }
                    }

                    return [];
                }
            );

        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getApplicationById(8);
        $this->assertEmpty($actualResult);

        /** @var ApplicationEntity $actualResult */
        $actualResult = $storage->getApplicationById(1);
        $this->assertInstanceOf(ApplicationEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals($data[0]['name'], $actualResult->getName());
        $this->assertEquals($data[0]['title'], $actualResult->getTitle());
        $this->assertTrue($actualResult->getReadOnly());

        $actualData = $this->invokePrivateMethod($storage, 'getEntityData', [$actualResult]);
        $this->assertArraysAreSimilar($data[0], $actualData);
    }

    /**
     * Test the getApplicationByName method.
     */
    public function testGetApplicationByName()
    {
        $data = $this->data;

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

        $dataEntity = new ApplicationEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new ApplicationStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getApplicationByName('someApplication');
        $this->assertEmpty($actualResult);

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
