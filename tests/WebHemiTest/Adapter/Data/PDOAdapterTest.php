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
use Prophecy\Argument;
use WebHemi\Adapter\Data\PDO\PDOAdapter;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Exception\InitException;
use WebHemi\Adapter\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class PDOAdapterTest.
 */
class PDOAdapterTest extends TestCase
{
    /** @var PDO */
    protected $pdo;

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
        $adapter = new PDOAdapter(new DateTime());
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
}
