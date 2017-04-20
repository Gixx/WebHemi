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
namespace WebHemiTest\Data\Connector;

use InvalidArgumentException;
use WebHemi\Data\Connector\MultiConnectorContainer;
use WebHemi\Data\Connector\PDO\MySQL\ConnectorAdapter as MySQLAdapter;
use WebHemi\Data\Connector\PDO\MySQL\DriverAdapter as MySQLDriver;
use WebHemi\Data\DriverInterface as DataDriverInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\SkippedTestError;

class MultiConnectorContainerTest extends TestCase
{
    /** @var DataDriverInterface */
    protected $dataDriver;

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
        $databaseFile = realpath(__DIR__ . '/../../../../build/gps-tool_schema.sqlite3');
        // The trick is that the MySQLDriver is a simple extension to the PDO class, so it can be used for the SQLite
        // as well without any issue.
        $this->dataDriver = new MySQLDriver('sqlite:' . $databaseFile);
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $adapter1 = new MySQLAdapter('default', $this->dataDriver);
        $adapter2 = new MySQLAdapter('somethingElse', $this->dataDriver);
        $adapter3 = new MySQLAdapter('a-new-one', $this->dataDriver);
        $adapter4 = new MySQLAdapter('noSql', $this->dataDriver);
        $adapter5 = new MySQLAdapter('default', $this->dataDriver);

        $testObj = new MultiConnectorContainer($adapter1);
        $this->assertAttributeCount(1, 'connectors', $testObj);

        $testObj = new MultiConnectorContainer($adapter1, $adapter2, $adapter3);
        $this->assertAttributeCount(3, 'connectors', $testObj);

        $testObj = new MultiConnectorContainer($adapter1, $adapter2, $adapter3, $adapter4, $adapter5);
        $this->assertAttributeCount(4, 'connectors', $testObj);
    }

    /**
     * Tests the getConnectorByName() method.
     */
    public function testGetConnectorByName()
    {
        $adapter1 = new MySQLAdapter('default', $this->dataDriver);
        $adapter2 = new MySQLAdapter('somethingElse', $this->dataDriver);
        $adapter3 = new MySQLAdapter('a-new-one', $this->dataDriver);

        $testObj = new MultiConnectorContainer($adapter1, $adapter2, $adapter3);
        $this->assertAttributeCount(3, 'connectors', $testObj);

        $actualConnector = $testObj->getConnectorByName('somethingElse');
        $this->assertSame('somethingElse', $actualConnector->getConnectorName());
        $this->assertInstanceOf(MySQLAdapter::class, $actualConnector);

        $this->expectException(InvalidArgumentException::class);
        $testObj->getConnectorByName('somethingNonExisting');
    }
}
