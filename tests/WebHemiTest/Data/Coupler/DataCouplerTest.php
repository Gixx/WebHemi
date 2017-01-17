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
namespace WebHemiTest\Data\Coupler;

use InvalidArgumentException;
use PDO;
use WebHemi\Adapter\Data\PDO\SQLiteAdapter;
use WebHemi\Adapter\Data\PDO\SQLiteDriver;
use WebHemi\Adapter\Data\DataDriverInterface;
use WebHemi\Data\Coupler;
use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_IncompleteTestError;
use PHPUnit_Framework_SkippedTestError;

/**
 * Class DataCouplerTest
 */
class DataCouplerTest extends TestCase
{
    /** @var DataDriverInterface */
    protected static $dataDriver;
    /** @var SQLiteAdapter */
    protected static $adapter;

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
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $databaseFile = realpath(__DIR__ . '/../../../../build/webhemi_schema.sqlite3');

        self::$dataDriver = new SQLiteDriver('sqlite:' . $databaseFile);

        $fixture = realpath(__DIR__.'/../../Fixtures/sql/data_coupler_test.sql');
        $setUpSql = file($fixture);

        if ($setUpSql) {
            foreach ($setUpSql as $sql) {
                $result = self::$dataDriver->query($sql);

                if (!$result) {
                    throw new PHPUnit_Framework_IncompleteTestError(
                        'Cannot set up test database: '.json_encode(self::$dataDriver->errorInfo())
                    );
                }
            }
        }

        self::$adapter = new SQLiteAdapter(self::$dataDriver);
    }

    /**
     * Tests UserToPolicyCoupler class.
     *
     * According to the SQLite database and the coupler test SQL, the following connections are set:
     *
     *   user 90000 to policy 90000 and 90001
     *   user 90001 to policy 90001, 90002 and 90003
     *
     *   policy 90000 to user 90000
     *   policy 90001 to user 90000 and 90001
     *   policy 90002 to user 90001
     *   policy 90003 to user 90001
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     * @covers \WebHemi\Data\Coupler\UserToPolicyCoupler
     * @covers \WebHemi\Data\Coupler\Traits\UserEntityTrait
     * @covers \WebHemi\Data\Coupler\Traits\PolicyEntityTrait
     */
    public function testUserToPolicyCoupler()
    {
        $userEntityPrototype = new Entity\User\UserEntity();
        $policyEntityPrototype = new Entity\AccessManagement\PolicyEntity();
        $userStorage = new Storage\User\UserStorage(self::$adapter, $userEntityPrototype);

        $coupler = new Coupler\UserToPolicyCoupler(self::$adapter, $userEntityPrototype, $policyEntityPrototype);
        $this->assertInstanceOf(Coupler\UserToPolicyCoupler::class, $coupler);

        // test: user 90000
        $userEntity = $userStorage->getUserById(90000);
        $policies = $coupler->getEntityDependencies($userEntity);
        $this->assertEquals(2, count($policies));
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[0]);
        $this->assertEquals(90000, $policies[0]->getPolicyId());
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[1]);
        $this->assertEquals(90001, $policies[1]->getPolicyId());

        // test in backward: policy 90001
        $users = $coupler->getEntityDependencies($policies[1]);
        $this->assertEquals(2, count($users));
        $this->assertInstanceOf(Entity\User\UserEntity::class, $users[0]);
        $this->assertEquals($userEntity->getUserId(), $users[0]->getUserId());
        $this->assertEquals(90000, $users[0]->getUserId());
        $this->assertInstanceOf(Entity\User\UserEntity::class, $users[1]);
        $this->assertEquals(90001, $users[1]->getUserId());

        // test: user 90001
        $userEntity = $userStorage->getUserById(90001);
        $policies = $coupler->getEntityDependencies($userEntity);
        $this->assertEquals(3, count($policies));
        $this->assertEquals(90001, $policies[0]->getPolicyId());
        $this->assertEquals(90002, $policies[1]->getPolicyId());
        $this->assertEquals(90003, $policies[2]->getPolicyId());

        // test constructor with changed entity order
        $coupler = new Coupler\UserToPolicyCoupler(self::$adapter, $policyEntityPrototype, $userEntityPrototype);
        $this->assertInstanceOf(Coupler\UserToPolicyCoupler::class, $coupler);

        // test error
        $this->expectException(InvalidArgumentException::class);
        new Coupler\UserToPolicyCoupler(self::$adapter, $userEntityPrototype, $userEntityPrototype);
    }

    /**
     * Tests UserToGroupCoupler class.
     *
     * According to the SQLite database and the coupler test SQL, the following connections are set:
     *
     *   user 90000 to user_group 90000 and 90001
     *   user 90001 to user_group 90001
     *
     *   user_group 90000 to user 90000
     *   user_group 90001 to user 90000 and 90001
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     * @covers \WebHemi\Data\Coupler\UserToGroupCoupler
     * @covers \WebHemi\Data\Coupler\Traits\UserEntityTrait
     * @covers \WebHemi\Data\Coupler\Traits\UserGroupEntityTrait
     */
    public function testUserToGroupCoupler()
    {
        $userEntityPrototype = new Entity\User\UserEntity();
        $userGroupEntityPrototype = new Entity\User\UserGroupEntity();
        $userStorage = new Storage\User\UserStorage(self::$adapter, $userEntityPrototype);

        $coupler = new Coupler\UserToGroupCoupler(self::$adapter, $userEntityPrototype, $userGroupEntityPrototype);
        $this->assertInstanceOf(Coupler\UserToGroupCoupler::class, $coupler);

        // test: user 90000
        $userEntity = $userStorage->getUserById(90000);
        $groups = $coupler->getEntityDependencies($userEntity);
        $this->assertEquals(2, count($groups));
        $this->assertInstanceOf(Entity\User\UserGroupEntity::class, $groups[0]);
        $this->assertEquals(90000, $groups[0]->getUserGroupId());
        $this->assertInstanceOf(Entity\User\UserGroupEntity::class, $groups[1]);
        $this->assertEquals(90001, $groups[1]->getUserGroupId());

        // test backward: group 90000
        $users = $coupler->getEntityDependencies($groups[0]);
        $this->assertEquals(1, count($users));
        $this->assertInstanceOf(Entity\User\UserEntity::class, $users[0]);
        $this->assertEquals($userEntity->getUserId(), $users[0]->getUserId());

        // test backward: group 90001
        $users = $coupler->getEntityDependencies($groups[1]);
        $this->assertEquals(2, count($users));
        $this->assertInstanceOf(Entity\User\UserEntity::class, $users[0]);
        $this->assertEquals(90000, $users[0]->getUserId());
        $this->assertInstanceOf(Entity\User\UserEntity::class, $users[1]);
        $this->assertEquals(90001, $users[1]->getUserId());

        // test constructor with changed entity order
        $coupler = new Coupler\UserToGroupCoupler(self::$adapter, $userGroupEntityPrototype, $userEntityPrototype);
        $this->assertInstanceOf(Coupler\UserToGroupCoupler::class, $coupler);

        // test error
        $this->expectException(InvalidArgumentException::class);
        new Coupler\UserToGroupCoupler(self::$adapter, $userGroupEntityPrototype, $userGroupEntityPrototype);
    }

    /**
     * Tests UserGroupToPolicyCoupler class.
     *
     * According to the SQLite database and the coupler test SQL, the following connections are set:
     *
     *   user_group 90000 to policy 90000 and 90001
     *   user_group 90001 to policy 90002 and 90003
     *
     *   policy 90000 to user_group 90000
     *   policy 90001 to user_group 90000
     *   policy 90002 to user_group 90001
     *   policy 90003 to user_group 90001
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     * @covers \WebHemi\Data\Coupler\UserGroupToPolicyCoupler
     * @covers \WebHemi\Data\Coupler\Traits\UserGroupEntityTrait
     * @covers \WebHemi\Data\Coupler\Traits\PolicyEntityTrait
     */
    public function testUserGroupToPolicyCoupler()
    {
        $userGroupEntityPrototype = new Entity\User\UserGroupEntity();
        $policyEntityPrototype = new Entity\AccessManagement\PolicyEntity();
        $userGroupStorage = new Storage\User\UserGroupStorage(self::$adapter, $userGroupEntityPrototype);

        $coupler = new Coupler\UserGroupToPolicyCoupler(
            self::$adapter,
            $userGroupEntityPrototype,
            $policyEntityPrototype
        );
        $this->assertInstanceOf(Coupler\UserGroupToPolicyCoupler::class, $coupler);

        // test: group 90000
        $userGroupEntity = $userGroupStorage->getUserGroupById(90000);
        $policies = $coupler->getEntityDependencies($userGroupEntity);
        $this->assertEquals(2, count($policies));
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[0]);
        $this->assertEquals(90000, $policies[0]->getPolicyId());
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[1]);
        $this->assertEquals(90001, $policies[1]->getPolicyId());

        // test backward: policy 90001
        $userGroups = $coupler->getEntityDependencies($policies[1]);
        $this->assertEquals(1, count($userGroups));
        $this->assertInstanceOf(Entity\User\UserGroupEntity::class, $userGroups[0]);
        $this->assertEquals($userGroupEntity->getUserGroupId(), $userGroups[0]->getUserGroupId());
        $this->assertEquals(90000, $userGroups[0]->getUserGroupId());

        // test: group 90001
        $userGroupEntity = $userGroupStorage->getUserGroupById(90001);
        $policies = $coupler->getEntityDependencies($userGroupEntity);
        $this->assertEquals(2, count($policies));
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[0]);
        $this->assertEquals(90002, $policies[0]->getPolicyId());
        $this->assertInstanceOf(Entity\AccessManagement\PolicyEntity::class, $policies[1]);
        $this->assertEquals(90003, $policies[1]->getPolicyId());

        // test constructor with changed entity order
        $coupler = new Coupler\UserGroupToPolicyCoupler(
            self::$adapter,
            $policyEntityPrototype,
            $userGroupEntityPrototype
        );
        $this->assertInstanceOf(Coupler\UserGroupToPolicyCoupler::class, $coupler);

        // test error
        $this->expectException(InvalidArgumentException::class);
        new Coupler\UserGroupToPolicyCoupler(self::$adapter, $userGroupEntityPrototype, $userGroupEntityPrototype);
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $fixture = realpath(__DIR__.'/../../Fixtures/sql/data_coupler_test.rollback.sql');
        $tearDownSql = file($fixture);

        if ($tearDownSql) {
            foreach ($tearDownSql as $sql) {
                self::$dataDriver->query($sql);
            }
        }
    }
}
