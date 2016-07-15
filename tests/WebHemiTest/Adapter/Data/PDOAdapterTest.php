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

use PDO;
use DateTime;
use WebHemi\Adapter\Data\PDO\PDOAdapter;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Exception\InitException;
use WebHemi\Adapter\Exception\InvalidArgumentException;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class PDOAdapterTest.
 */
class PDOAdapterTest extends TestCase
{
    /** @var PDO */
    protected $pdo;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Check requirements - also checks SQLite availability.
     */
    protected function checkRequirements()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('No SQLite Available');
        }

        return parent::checkRequirements();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $databaseFile = realpath(__DIR__ . '/../../Fixtures/database.sqlite3');

        $this->pdo = new PDO('sqlite:' . $databaseFile);
    }

    /**
     * Tests constructor.
     *
     * @throws InvalidArgumentException
     */
    public function testConstructor()
    {
        $adapter = new PDOAdapter($this->pdo);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);
        $this->assertAttributeEmpty('dataGroup', $adapter);
        $this->assertAttributeEmpty('idKey', $adapter);

        $this->setExpectedException(InvalidArgumentException::class);
        new PDOAdapter(new DateTime());
    }

    /**
     * Tests the getDataStorage method.
     */
    public function testGetDataStorage()
    {
        $adapter = new PDOAdapter($this->pdo);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $actualStorage = $adapter->getDataStorage();
        $this->assertInstanceOf(PDO::class, $actualStorage);
        $this->assertTrue($this->pdo === $actualStorage);
    }

    /**
     * Tests the setDataGroup method.
     *
     * @throws InitException
     */
    public function testSetDataGroup()
    {
        $adapter = new PDOAdapter($this->pdo);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $result = $adapter->setDataGroup('webhemi_user');
        $this->assertInstanceOf(DataAdapterInterface::class, $result);
        $this->assertTrue($adapter === $result);

        $this->setExpectedException(InitException::class);
        $adapter->setDataGroup('shouldBeBad');
    }

    /**
     * Tests setIdKey method.
     *
     * @throws InitException
     */
    public function testSetIdKey()
    {
        $adapter = new PDOAdapter($this->pdo);
        $this->assertInstanceOf(DataAdapterInterface::class, $adapter);

        $result = $adapter->setIdKey('id_user');
        $this->assertInstanceOf(DataAdapterInterface::class, $result);
        $this->assertTrue($adapter === $result);

        $this->setExpectedException(InitException::class);
        $adapter->setIdKey('shouldBeBad');
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
                'SELECT * FROM bTable WHERE A=? LIMIT 11 OFFSET 20',
                [5]
            ],
            [
                ['A' => 10, 'B LIKE ?' => 'someData%'],
                'cTable',
                null,
                null,
                'SELECT * FROM cTable WHERE A=? AND B LIKE ? LIMIT '.PDOAdapter::DATA_SET_RECORD_LIMIT.' OFFSET 0',
                [10, 'someData%']
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

        $adapter = new PDOAdapter($this->pdo);
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
            [['A' => 5], ' WHERE A=?', [5]],
            [['A' => 10, 'B LIKE ?' => 'someData%'], ' WHERE A=? AND B LIKE ?', [10, 'someData%']]
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

        $adapter = new PDOAdapter($this->pdo);
        $resultExpression = $this->invokePrivateMethod($adapter, 'getWhereExpression', [$expression, &$queryBind]);

        $this->assertEquals($expectedExpression, $resultExpression);
        $this->assertArraysAreSimilar($expectedQueryBind, $queryBind);
    }
}
