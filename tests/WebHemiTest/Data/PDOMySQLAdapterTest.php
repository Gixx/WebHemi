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
namespace WebHemiTest\Data;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use WebHemi\Data\Connector\PDO\MySQL\ConnectorAdapter as MySQLAdapter;
use WebHemi\Data\Connector\PDO\MySQL\DriverAdapter as MySQLDriver;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Data\DriverInterface as DataDriverInterface;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\SkippedTestError;

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
            throw new SkippedTestError('No SQLite Available');
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
        $databaseFile = realpath(__DIR__ . '/../../../build/webhemi_schema.sqlite3');
        // The trick is that the MySQLDriver is a simple extension to the PDO class, so it can be used for the SQLite
        // as well without any issue.
        $this->dataDriver = new MySQLDriver('sqlite:' . $databaseFile);
    }

    /**
     * Tests constructor.
     *
     * @throws InvalidArgumentException
     */
    public function testConstructor()
    {
        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEmpty('dataGroup', $adapter);
        $this->assertAttributeEmpty('idKey', $adapter);
        $this->assertSame('unit-test', $adapter->getConnectorName());

        /** @var DataDriverInterface $fakeDriver */
        $fakeDriver = $this->prophesize(DataDriverInterface::class);

        $this->expectException(InvalidArgumentException::class);
        new MySQLAdapter('unit-test2', $fakeDriver->reveal());
    }

    /**
     * Tests the getDataDriver method.
     */
    public function testGetDataDriver()
    {
        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $actualStorage = $adapter->getDataDriver();
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
        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
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
        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $result = $adapter->setIdKey('id_user');
        $this->assertInstanceOf(DataAdapterInterface::class, $result);
        $this->assertTrue($adapter === $result);
    }

    /**
     * Data provider for the Query test.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function sqlQueryDataProvider()
    {
        return [
            [
                [],
                'aTable',
                1,
                5,
                null,
                'groupCol',
                null,
                'SELECT * FROM aTable GROUP BY groupCol ORDER BY id LIMIT 1 OFFSET 5',
                []
            ],
            [
                ['A' => 5],
                'bTable',
                11,
                20,
                'orderCol',
                null,
                null,
                'SELECT * FROM bTable WHERE A = ? ORDER BY orderCol LIMIT 11 OFFSET 20',
                [5]
            ],
            [
                ['A' => 10, 'B LIKE ?' => 'someData%'],
                'cTable',
                null,
                1,
                null,
                null,
                'orderCol = 1',
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? ORDER BY id',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B LIKE' => 'someData%'],
                'cTable',
                null,
                null,
                'orderCol',
                'groupCol',
                'orderCol > 1',
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? GROUP BY groupCol HAVING orderCol > 1 ORDER BY orderCol',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B' => 'someData%'],
                'cTable',
                null,
                null,
                null,
                null,
                'orderCol = 1',
                'SELECT * FROM cTable WHERE A = ? AND B LIKE ? ORDER BY id',
                [10, 'someData%']
            ],
            [
                ['A' => 10, 'B IN (?)' => [1,2,3]],
                'cTable',
                3,
                0,
                'orderCol',
                null,
                'orderCol = 1',
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) ORDER BY orderCol LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ],
            [
                ['A' => 10, 'B IN ?' => [1,2,3]],
                'cTable',
                3,
                0,
                null,
                null,
                null,
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) ORDER BY id LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ],
            [
                ['A' => 10, 'B' => [1,2,3]],
                'cTable',
                3,
                0,
                null,
                'groupCol',
                null,
                'SELECT * FROM cTable WHERE A = ? AND B IN (?,?,?) GROUP BY groupCol ORDER BY id LIMIT 3 OFFSET 0',
                [10, 1, 2, 3]
            ],
            [
                ['A' => null],
                'cTable',
                null,
                null,
                null,
                'groupCol',
                null,
                'SELECT * FROM cTable WHERE A IS NULL GROUP BY groupCol ORDER BY id',
                []
            ],
            [
                ['A' => false],
                'cTable',
                null,
                null,
                null,
                'groupCol',
                null,
                'SELECT * FROM cTable WHERE A IS NULL GROUP BY groupCol ORDER BY id',
                []
            ],
            [
                ['A' => true],
                'cTable',
                null,
                null,
                null,
                'groupCol',
                null,
                'SELECT * FROM cTable WHERE A IS NOT NULL GROUP BY groupCol ORDER BY id',
                []
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
        $order,
        $group,
        $having,
        $expectedQuery,
        $expectedQueryBind
    ) {
        $queryBind = [];

        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
        $adapter->setDataGroup($dataGroup);
        $adapter->setIdKey('id');

        $options = [];

        if (!is_null($group)) {
            $options[DataAdapterInterface::OPTION_GROUP] = $group;
            $options[DataAdapterInterface::OPTION_HAVING] = $having ?? '';
        }

        if (!is_null($order)) {
            $options[DataAdapterInterface::OPTION_ORDER] = $order;
        }

        if (!is_null($limit)) {
            $options[DataAdapterInterface::OPTION_LIMIT] = (int)$limit;
            $options[DataAdapterInterface::OPTION_OFFSET] = (int)$offset;
        }

        $resultQuery = $this->invokePrivateMethod(
            $adapter,
            'getSelectQueryForExpression',
            [
                $expression,
                &$queryBind,
                $options
            ]
        );

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

        $adapter = new MySQLAdapter('unit-test', $this->dataDriver);
        $resultExpression = $this->invokePrivateMethod($adapter, 'getWhereExpression', [$expression, &$queryBind]);

        $this->assertEquals($expectedExpression, $resultExpression);
        $this->assertArraysAreSimilar($expectedQueryBind, $queryBind);
    }
}
