<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Adapter\Data;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;
use RuntimeException;
use WebHemi\Adapter\Data\InMemory\InMemoryAdapter;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\InMemory\InMemoryDriver;
use WebHemiTest\AssertTrait;

/**
 * Class InMemoryAdapterTest.
 */
class InMemoryAdapterTest extends TestCase
{
    protected $arrayDatabase;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->arrayDatabase = include __DIR__ . '/../../Fixtures/InMemoryProperData.php';
    }

    /**
     * Prepares the adapter for tests.
     *
     * @param InMemoryAdapter $adapter
     * @return InMemoryAdapter
     */
    protected function prepareDataStoreWithProperData($adapter)
    {
        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];
        $adapter->setDataGroup('webhemi_user_meta');
        $adapter->setIdKey('id_user_meta');

        // Save new elements.
        foreach ($expectedArray as $data) {
            $adapter->saveData(null, $data);
        }

        return $adapter;
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            [[], ['default' => []]],
            [
                [['someKey' => 'someData'],['someOtherData']],
                ['default' => [0 => ['someKey' => 'someData'], 1 => ['someOtherData']]]
            ],
            [['shouldBeBad'], InvalidArgumentException::class]
        ];
    }

    /**
     * Tests constructor with different parameters.
     *
     * @param string $argument
     * @param array  $expectedResult
     *
     * @throws InvalidArgumentException
     *
     * @dataProvider dataProvider
     */
    public function testConstructor($argument, $expectedResult)
    {
        if ($expectedResult == InvalidArgumentException::class) {
            $this->setExpectedException(InvalidArgumentException::class);
        }

        $driver = new InMemoryDriver($argument);
        $adapter = new InMemoryAdapter($driver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEquals('id', 'idKey', $adapter);
        $this->assertAttributeEquals('default', 'dataGroup', $adapter);
        $this->assertArraysAreSimilar($expectedResult, $adapter->getDataStorage());
    }

    /**
     * Tests the setDataGroup method.
     *
     * @throws RuntimeException
     */
    public function testSetDataGroup()
    {
        $argument = [
            ['someKey' => 'someData'],
            ['someOtherData']
        ];
        $expectedDefault = [
            'default' => [
                0 => ['someKey' => 'someData'],
                1 => ['someOtherData']]
        ];
        $expectedChanged = [
            'notDefault' => [
                0 => ['someKey' => 'someData'],
                1 => ['someOtherData']]
        ];

        $driver = new InMemoryDriver($argument);
        $adapter = new InMemoryAdapter($driver);

        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEquals('default', 'dataGroup', $adapter);
        $this->assertArraysAreSimilar($adapter->getDataStorage(), $expectedDefault);

        $adapter->setDataGroup('notDefault');
        $this->assertAttributeEquals('notDefault', 'dataGroup', $adapter);
        $this->assertArraysAreSimilar($adapter->getDataStorage(), $expectedChanged);

        $this->setExpectedException(RuntimeException::class);
        $adapter->setDataGroup('shouldBeBad');
    }

    /**
     * Tests setIdKey method.
     *
     * @throws RuntimeException
     */
    public function testSetIdKey()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());

        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEquals('id', 'idKey', $adapter);

        $adapter->setIdKey('someOtherId');
        $this->assertAttributeEquals('someOtherId', 'idKey', $adapter);

        $this->setExpectedException(RuntimeException::class);
        $adapter->setIdKey('shouldBeBad');
    }

    /**
     * Test saving data.
     */
    public function testSaveData()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());

        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];
        $adapter->setDataGroup('webhemi_user_meta');
        $adapter->setIdKey('id_user_meta');

        // Save new elements.
        foreach ($expectedArray as $expectedIdentifier => $data) {
            $actualIdentifier = $adapter->saveData(null, $data);
            $this->assertEquals($actualIdentifier, $expectedIdentifier);
        }

        $actualArray = $adapter->getDataStorage()['webhemi_user_meta'];
        $this->assertArraysAreSimilar($actualArray, $expectedArray);

        // Update existing element.
        $updateId = 3;
        $expectedArray[$updateId]['meta_data'] = 'The Amazing Spider-man';
        $actualIdentifier = $adapter->saveData($updateId, $expectedArray[$updateId]);
        $this->assertEquals($actualIdentifier, $updateId);
        $actualArray = $adapter->getDataStorage()['webhemi_user_meta'];
        $this->assertArraysAreSimilar($actualArray, $expectedArray);

        // Test string identifier.
        $stringIdentifier = 'extra_id';
        $expectedArray[$stringIdentifier] = ['extra_key' => 'flexible'];
        $adapter->saveData($stringIdentifier, $expectedArray[$stringIdentifier]);
        $actualArray = $adapter->getDataStorage()['webhemi_user_meta'];
        $this->assertArraysAreSimilar($actualArray, $expectedArray);

        // Update existing string identifier.
        $expectedArray[$stringIdentifier]['extra_key'] = 'super flexible';
        $actualIdentifier = $adapter->saveData($stringIdentifier, $expectedArray[$stringIdentifier]);
        $actualArray = $adapter->getDataStorage()['webhemi_user_meta'];
        $this->assertArraysAreSimilar($actualArray, $expectedArray);
        $this->assertSame($actualIdentifier, $stringIdentifier);

        // The new data must have extended string identifier.
        $expectedIdentifier = 'extra_id_1';
        $actualIdentifier = $adapter->saveData(null, ['someData']);
        $this->assertSame($actualIdentifier, $expectedIdentifier);

        // Repeating will extend towards.
        $expectedIdentifier = 'extra_id_1_1';
        $actualIdentifier = $adapter->saveData(null, ['someData2']);
        $this->assertSame($actualIdentifier, $expectedIdentifier);
    }

    /**
     * Tests getting the data.
     */
    public function testGetData()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());

        $adapter = $this->prepareDataStoreWithProperData($adapter);
        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];

        for ($i = 1; $i < 5; $i++) {
            $actualArray = $adapter->getData($i);
            $this->assertArraysAreSimilar($actualArray, $expectedArray[$i]);
        }

        $actualArray = $adapter->getData(9999);
        $this->assertEmpty($actualArray);
        $this->assertEquals($actualArray, []);
    }

    /**
     * Tests data deletion.
     */
    public function testDeleteData()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());
        $adapter = $this->prepareDataStoreWithProperData($adapter);
        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];

        for ($i = 1; $i < 5; $i++) {
            unset($expectedArray[$i]);
            $actualResult = $adapter->deleteData($i);
            $this->assertTrue($actualResult);
            $actualArray = $adapter->getDataStorage()['webhemi_user_meta'];
            $this->assertArraysAreSimilar($actualArray, $expectedArray);
        }

        $actualResult = $adapter->deleteData(9999);
        $this->assertFalse($actualResult);
    }

    /**
     * Test list and count functions when there are no special expressions.
     */
    public function testDataSetWithoutExpressions()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());
        $adapter = $this->prepareDataStoreWithProperData($adapter);
        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];

        $actualCount = $adapter->getDataCardinality([]);
        $expectedCount = count($expectedArray);
        $this->assertEquals($actualCount, $expectedCount);

        // Test limit.
        $expectedData = [$expectedArray[1], $expectedArray[2]];
        $actualData = $adapter->getDataSet([], 2);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test limit with offset.
        // Offset 2 two means it skips 2 and returns the amount of limit bebinning with the third.
        $expectedData = [$expectedArray[3]];
        $actualData = $adapter->getDataSet([], 1, 2);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test over limit.
        $expectedData = [$expectedArray[1], $expectedArray[2], $expectedArray[3], $expectedArray[4]];
        $actualData = $adapter->getDataSet([], 100);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test over offset.
        $expectedData = [];
        $actualData = $adapter->getDataSet([], 1, 100);
        $this->assertArraysAreSimilar($actualData, $expectedData);
    }

    /**
     * Tests list and count functions with various relational expressions.
     */
    public function testDataSetExpressions()
    {
        $adapter = new InMemoryAdapter(new InMemoryDriver());
        $adapter = $this->prepareDataStoreWithProperData($adapter);
        $expectedArray = $this->arrayDatabase['webhemi_user_meta'];

        // Test LIKE expression.
        $expectedData = [$expectedArray[1], $expectedArray[3]];
        $expression = ['meta_data LIKE ?' => '%man'];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test LIKE expression with limit and offset.
        $expectedData = [$expectedArray[3], $expectedArray[4]];
        // Also fits to 'human'...
        $expression = ['meta_data LIKE ?' => '%man%'];
        $actualData = $adapter->getDataSet($expression, 2, 1);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test IN expression.
        $expectedData = [$expectedArray[1], $expectedArray[4]];
        $expression = ['id_user_meta IN (?)' => [1, 4]];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Test IN expression with limit and offset, no brackets in pattern.
        $expectedData = [$expectedArray[3], $expectedArray[4]];
        $expression = ['id_user_meta IN ?' => [1, 3, 4]];
        $actualData = $adapter->getDataSet($expression, 5, 1);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        // Limit and offset and complex relations work, now test only simple relations now.
        $expectedData = [$expectedArray[3]];
        $expression = ['id_user_meta' => 3];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        $expectedData = [$expectedArray[3], $expectedArray[4]];
        $expression = ['id_user_meta > ?' => 2];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        $expectedData = [$expectedArray[2], $expectedArray[3], $expectedArray[4]];
        $expression = ['id_user_meta >= ?' => 2];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        $expectedData = [$expectedArray[3]];
        $expression = ['id_user_meta < ?' => 4, 'fk_user' => 2];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        $expectedData = [$expectedArray[1], $expectedArray[3]];
        $expression = ['id_user_meta <= ?' => 3, 'meta_key' => 'alter ego'];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);

        $expectedData = [$expectedArray[1], $expectedArray[3], $expectedArray[4]];
        $expression = ['id_user_meta <> ?' => 2];
        $actualData = $adapter->getDataSet($expression);
        $this->assertArraysAreSimilar($actualData, $expectedData);
    }
}
