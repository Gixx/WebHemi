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
namespace WebHemiTest\Data\Coupler;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use WebHemi\Data\Connector\PDO\SQLite\ConnectorAdapter as SQLiteAdapter;
use WebHemi\Data\Connector\PDO\SQLite\DriverAdapter as SQLiteDriver;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemiTest\TestService\EmptyCoupler;
use WebHemiTest\TestService\EmptyEntity;
use WebHemiTest\TestService\EmptyEntity2;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

/**
 * Class GeneralCouplerTest. Tests the AbstractDataCoupler's methods mostly.
 */
class GeneralCouplerTest extends TestCase
{
    /** @var SQLiteDriver */
    protected static $dataDriver;
    /** @var SQLiteAdapter */
    protected static $adapter;

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
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $databaseFile = realpath(__DIR__ . '/../../../../build/webhemi_schema.sqlite3');

        self::$dataDriver = new SQLiteDriver('sqlite:' . $databaseFile);
        self::$adapter = new SQLiteAdapter('unit-test', self::$dataDriver);
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Coupler\AbstractCoupler
     */
    public function testConstructor()
    {
        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();

        // Test constructor: The EmptyCoupler requires EmptyEntities
        $coupler = new EmptyCoupler(self::$adapter, $entityA, $entityB);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);

        // Test constructor: The EmptyCoupler requires EmptyEntities, changed order
        $coupler2 = new EmptyCoupler(self::$adapter, $entityB, $entityA);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler2);

        // Test constructor error:
        $applicationEntity = new ApplicationEntity();
        $this->expectException(InvalidArgumentException::class);
        new EmptyCoupler(self::$adapter, $applicationEntity, $entityA);
    }

    /**
     * Test coupler methods.
     *
     * @covers \WebHemi\Data\Coupler\AbstractCoupler
     */
    public function testCouplerFunctions()
    {
        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();

        $coupler = new EmptyCoupler(self::$adapter, $entityA, $entityB);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);
        $this->assertInstanceOf(DataAdapterInterface::class, $coupler->getConnector());

        $newEntity = $this->invokePrivateMethod($coupler, 'getNewEntityInstance', [EmptyEntity::class]);
        $this->assertInstanceOf(EmptyEntity::class, $newEntity);
        $this->assertFalse($entityA === $newEntity);

        $newEntity = $this->invokePrivateMethod($coupler, 'getNewEntityInstance', [EmptyEntity2::class]);
        $this->assertInstanceOf(EmptyEntity2::class, $newEntity);
        $this->assertFalse($entityB === $newEntity);
    }

    /**
     * Tests getEntityDependencies() method
     *
     * @covers \WebHemi\Data\Coupler\AbstractCoupler
     */
    public function testDependencyResolver()
    {
        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();
        $coupler = new EmptyCoupler(self::$adapter, $entityA, $entityB);

        $entityList = $coupler->getEntityDependencies($entityA);
        $this->assertEquals(3, count($entityList));
        $this->assertInstanceOf(EmptyEntity::class, $entityList[0]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[1]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[2]);

        $entityList = $coupler->getEntityDependencies($entityB);
        $this->assertEquals(2, count($entityList));
        $this->assertInstanceOf(EmptyEntity2::class, $entityList[0]);
        $this->assertInstanceOf(EmptyEntity2::class, $entityList[1]);

        $this->expectException(RuntimeException::class);
        $applicationEntity = new ApplicationEntity();
        $coupler->getEntityDependencies($applicationEntity);
    }

    /**
     * Tests setDependency() method
     */
    public function testDependencySetter()
    {
        $this->runFixtureQueries(realpath(__DIR__.'/../../TestData/sql/general_coupler_test.sql'));

        $entityA = new EmptyEntity('empty_id_1');
        $entityA->setKeyData(1);
        $entityB = new EmptyEntity2('empty_id_2');
        $entityB->setKeyData(3);
        $entityC = new EmptyEntity2('empty_id_3');
        $entityC->setKeyData(3);
        $coupler = new EmptyCoupler(self::$adapter, $entityA, $entityB);

        $saveDataId = $coupler->setDependency($entityA, $entityB);
        $this->assertEquals(1, $saveDataId);

        $saveDataId = $coupler->setDependency($entityA, $entityC);
        $this->assertEquals(2, $saveDataId);


        $coupler = new EmptyCoupler(self::$adapter, $entityA, $entityB);
        $applicationEntity = new ApplicationEntity();

        // Set dependency for wrong data entity #1
        try {
            $coupler->setDependency($applicationEntity, $entityB);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1002, $e->getCode());
        }

        // Set dependency for wrong data entity #2
        try {
            $coupler->setDependency($entityA, $applicationEntity);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1003, $e->getCode());
        }

        // Set dependency for self
        try {
            $coupler->setDependency($entityA, $entityA);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1004, $e->getCode());
        }

        $this->runFixtureQueries(realpath(__DIR__.'/../../TestData/sql/general_coupler_test.rollback.sql'));
    }

    /**
     * Runs queries
     *
     * @param string $fixtureFile
     */
    private function runFixtureQueries($fixtureFile)
    {
        $setUpSql = file($fixtureFile);

        if ($setUpSql) {
            foreach ($setUpSql as $sql) {
                $result = self::$dataDriver->query($sql);

                if (!$result) {
                    throw new IncompleteTestError(
                        'Cannot run query: '.$sql.'; Error: '.json_encode(self::$dataDriver->errorInfo())
                    );
                }
            }
        }
    }
}
