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
namespace WebHemiTest\Adapter\Data;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use WebHemi\Adapter\Data\PDO\MySQLAdapter;
use WebHemi\Adapter\Data\InMemory\InMemoryDriver;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\DataDriverInterface;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use WebHemiTest\Fixtures\EmptySqliteDataDriver;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_SkippedTestError;

/**
 * Class MySQLAdapterTest.
 */
class PDOMySQLAdapterTest extends TestCase
{
    /** @var DataDriverInterface */
    protected $dataDriver;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Check requirements - also checks SQLite availability.
     */
    protected function checkRequirements()
    {
        if (!extension_loaded('pdo_sqlite')) {
            throw new PHPUnit_Framework_SkippedTestError('No SQLite Available');
        }

        parent::checkRequirements();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $databaseFile = realpath(__DIR__ . '/../../../../build/webhemi_schema.sqlite3');

        $this->dataDriver = new EmptySqliteDataDriver('sqlite:' . $databaseFile);
    }

    /**
     * Tests constructor.
     *
     * @throws InvalidArgumentException
     */
    public function testConstructor()
    {
        $adapter = new MySQLAdapter($this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEmpty('dataGroup', $adapter);
        $this->assertAttributeEmpty('idKey', $adapter);

        $this->setExpectedException(InvalidArgumentException::class);
        new MySQLAdapter(new InMemoryDriver());
    }

    /**
     * Tests the getDataStorage method.
     */
    public function testGetDataStorage()
    {
        $adapter = new MySQLAdapter($this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $actualStorage = $adapter->getDataStorage();
        $this->assertInstanceOf(PDO::class, $actualStorage);
        $this->assertTrue($this->dataDriver === $actualStorage);
    }

    /**
     * Tests the setDataGroup method.
     *
     * @throws RuntimeException
     */
    public function testSetDataGroup()
    {
        $adapter = new MySQLAdapter($this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $result = $adapter->setDataGroup('webhemi_user');
        $this->assertInstanceOf(DataAdapterInterface::class, $result);
        $this->assertTrue($adapter === $result);
    }

    /**
     * Tests setIdKey method.
     *
     * @throws RuntimeException
     */
    public function testSetIdKey()
    {
        $adapter = new MySQLAdapter($this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $result = $adapter->setIdKey('id_user');
        $this->assertInstanceOf(DataAdapterInterface::class, $result);
        $this->assertTrue($adapter === $result);
    }

    /**
     * Data provider for the Query test.
     *
     * @return array
     */
    public function sqlQueryDataProvider()
    {
        return [
            [
                [],
                'aTable',
                1,
                5,
                'SELECT * FROM aTable LIMIT 1 OFFSET 5',
                []
            ],
            [
                ['A' => 5],
                'bTable',
                11,
                20,
                'SELECT * FROM bTable WHERE A = ? LIMIT 11 OFFSET 20',
                [5]
            ],
            [
                ['A' => 10, 'B LIKE ?' => 'someData%'],
                'cTable',
                null,
                null,
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? LIMIT '.PHP_INT_MAX.' OFFSET 0',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B LIKE' => 'someData%'],
                'cTable',
                null,
                null,
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? LIMIT '.PHP_INT_MAX.' OFFSET 0',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B' => 'someData%'],
                'cTable',
                null,
                null,
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? LIMIT '.PHP_INT_MAX.' OFFSET 0',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B IN (?)' => [1,2,3]],
                'cTable',
                3,
                0,
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ],
            [
                ['A' => 10, 'B IN ?' => [1,2,3]],
                'cTable',
                3,
                0,
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ],
            [
                ['A' => 10, 'B' => [1,2,3]],
                'cTable',
                3,
                0,
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ]
        ];
    }

    /**
     * Tests WHERE expression generator.
     *
     * @param array  $expression
     * @param string $dataGroup
     * @param int    $limit
     * @param int    $offset
     * @param string $expectedQuery
     * @param array  $expectedQueryBind
     *
     * @dataProvider sqlQueryDataProvider
     */
    public function testGetQueryForExpression(
        $expression,
        $dataGroup,
        $limit,
        $offset,
        $expectedQuery,
        $expectedQueryBind
    ) {
        $queryBind = [];

        $adapter = new MySQLAdapter($this->dataDriver);
        $adapter->setDataGroup($dataGroup);

        if (!is_null($limit)) {
            $resultQuery = $this->invokePrivateMethod(
                $adapter,
                'getSelectQueryForExpression',
                [$expression, &$queryBind, $limit, $offset]
            );
        } else {
            $resultQuery = $this->invokePrivateMethod(
                $adapter,
                'getSelectQueryForExpression',
                [$expression, &$queryBind]
            );
        }

        $this->assertEquals($expectedQuery, $resultQuery);
        $this->assertArraysAreSimilar($expectedQueryBind, $queryBind);
    }

    /**
     * Data provider for the WHERE expression test.
     *
     * @return array
     */
    public function whereExpressionDataProvider()
    {
        return [
            [[], '', []],
            [['A' => 5], ' WHERE A = ?', [5]],
            [['A' => 10, 'B LIKE ?' => 'someData%'], ' WHERE A = ? AND B LIKE ?', [10, 'someData%']],
            [['A' => 10, 'B' => [1,2,3]], ' WHERE A = ? AND B IN (?,?,?)', [10, 1, 2, 3]],
        ];
    }

    /**
     * Tests WHERE expression generator.
     *
     * @param array  $expression
     * @param string $expectedExpression
     * @param array  $expectedQueryBind
     *
     * @dataProvider whereExpressionDataProvider
     */
    public function testGetWhereExpression($expression, $expectedExpression, $expectedQueryBind)
    {
        $queryBind = [];

        $adapter = new MySQLAdapter($this->dataDriver);
        $resultExpression = $this->invokePrivateMethod($adapter, 'getWhereExpression', [$expression, &$queryBind]);

        $this->assertEquals($expectedExpression, $resultExpression);
        $this->assertArraysAreSimilar($expectedQueryBind, $queryBind);
    }
}
