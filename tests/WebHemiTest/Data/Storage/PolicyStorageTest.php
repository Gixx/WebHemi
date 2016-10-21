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
use WebHemi\Data\Storage\AccessManagement\PolicyStorage;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class PolicyStorageTest.
 */
class PolicyStorageTest extends TestCase
{
    /** @var array */
    private $data;
    private $defaultAdapter;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        $this->data =  [
            0 => [
                'id_am_policy' => 1,
                'fk_am_resource' => 1,
                'fk_application' => 1,
                'title' => 'Test Policy 1',
                'description' => 'A test policy record',
                'is_read_only' => true,
                'is_allowed' => true,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  null,
            ],
            1 => [
                'id_am_policy' => 2,
                'fk_am_resource' => 1,
                'fk_application' => 2,
                'title' => 'Test Policy 2',
                'description' => 'A test policy record',
                'is_read_only' => false,
                'is_allowed' => false,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  null,
            ],
            2 => [
                'id_am_policy' => 3,
                'fk_am_resource' => null,
                'fk_application' => null,
                'title' => 'Test Policy 3',
                'description' => 'A test policy record',
                'is_read_only' => false,
                'is_allowed' => true,
                'date_created' =>  '2016-03-24 16:25:12',
                'date_modified' =>  null,
            ]
        ];

        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractDataStorage
     */
    public function testStorageInit()
    {
        $dataEntity = new PolicyEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new PolicyStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(PolicyStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_am_policy', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_am_policy', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(PolicyEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getPolicyById method.
     */
    public function testGetPolicyById()
    {
        $data = $this->data;

        $this->defaultAdapter
            ->getData(Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    $index = $args[0] - 1;
                    if (isset($data[$index])) {
                        return $data[$index];
                    }

                    return false;
                }
            );

        $dataEntity = new PolicyEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new PolicyStorage($defaultAdapterInstance, $dataEntity);

        $actualResult = $storage->getPolicyById(4);
        $this->assertFalse($actualResult);

        /** @var PolicyEntity $actualResult */
        $actualResult = $storage->getPolicyById(1);
        $this->assertInstanceOf(PolicyEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertInstanceOf(DateTime::class, $actualResult->getDateCreated());
        $this->assertEquals(1, $actualResult->getResourceId());
        $this->assertEquals(1, $actualResult->getApplicationId());
        $this->assertTrue($actualResult->getReadOnly());

        $actualResult = $storage->getPolicyById(3);
        $this->assertEmpty($actualResult->getResourceId());
        $this->assertTrue($actualResult->getAllowed());
    }

    /**
     * Test the getPoliciesByResourceId method.
     */
    public function testGetPoliciesByResourceId()
    {
        $data = $this->data;

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    $resourceId = $args[0]['fk_am_resource'];
                    if ($resourceId == 1) {
                        return [$data[0], $data[1]];
                    }

                    if (is_null($resourceId)) {
                        return [$data[2]];
                    }

                    return false;
                }
            );

        $dataEntity = new PolicyEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new PolicyStorage($defaultAdapterInstance, $dataEntity);

        /** @var array<PolicyEntity> $actualResult */
        $actualResult = $storage->getPoliciesByResourceId(1);
        $this->assertSame(2, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertSame('Test Policy 1', $actualResult[0]->getTitle());
        $this->assertTrue($actualResult[0]->getAllowed());
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[1]);
        $this->assertFalse($actualResult[1]->getAllowed());
        $this->assertSame('Test Policy 2', $actualResult[1]->getTitle());

        $actualResult = $storage->getPoliciesByResourceId(null);
        $this->assertSame(1, count($actualResult));
        $this->assertFalse($actualResult[0]->getReadOnly());
        $this->assertSame('Test Policy 3', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByResourceId(100);
        $this->assertEmpty($actualResult);
    }

    /**
     * Test the getPoliciesByApplicationId method.
     */
    public function testGetPoliciesByApplicationId()
    {
        $data = $this->data;

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    $applicationId = $args[0]['fk_application'];
                    if ($applicationId == 1) {
                        return [$data[0]];
                    }

                    if ($applicationId == 2) {
                        return [$data[1]];
                    }

                    if (is_null($applicationId)) {
                        return [$data[2]];
                    }

                    return false;
                }
            );

        $dataEntity = new PolicyEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new PolicyStorage($defaultAdapterInstance, $dataEntity);

        /** @var array<PolicyEntity> $actualResult */
        $actualResult = $storage->getPoliciesByApplicationId(1);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertTrue($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 1', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByApplicationId(2);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertFalse($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 2', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByApplicationId(null);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertTrue($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 3', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByApplicationId(100);
        $this->assertEmpty($actualResult);
    }

    public function testGetPoliciesByResourceAndApplicationIds()
    {
        $data = $this->data;

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    $resourceId = $args[0]['fk_am_resource'];
                    $applicationId = $args[0]['fk_application'];

                    if ($resourceId == 1 && $applicationId == 1) {
                        return [$data[0]];
                    }

                    if ($resourceId == 1 && $applicationId == 2) {
                        return [$data[1]];
                    }

                    if (is_null($resourceId) && is_null($applicationId)) {
                        return [$data[2]];
                    }

                    return false;
                }
            );

        $dataEntity = new PolicyEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new PolicyStorage($defaultAdapterInstance, $dataEntity);

        /** @var array<PolicyEntity> $actualResult */
        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(1, 1);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertTrue($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 1', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(1, 2);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertFalse($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 2', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(null, null);
        $this->assertSame(1, count($actualResult));
        $this->assertInstanceOf(PolicyEntity::class, $actualResult[0]);
        $this->assertTrue($actualResult[0]->getAllowed());
        $this->assertSame('Test Policy 3', $actualResult[0]->getTitle());

        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(1, 100);
        $this->assertEmpty($actualResult);

        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(1, 3);
        $this->assertEmpty($actualResult);

        $actualResult = $storage->getPoliciesByResourceAndApplicationIds(null, 1);
        $this->assertEmpty($actualResult);
    }
}
