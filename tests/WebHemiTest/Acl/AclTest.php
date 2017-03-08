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

namespace WebHemiTest\Acl;

use PDO;
use WebHemi\Acl\ServiceAdapter\Base\ServiceAdapter as Acl;
use WebHemi\Acl\ServiceInterface as AclAdapterInterface;
use WebHemi\Auth\ServiceAdapter\Base\ServiceAdapter as Auth;
use WebHemi\Auth\Result\Result;
use WebHemi\Auth\Credential\NameAndPasswordCredential;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Connector\PDO\SQLite\ConnectorAdapter as SQLiteAdapter;
use WebHemi\Data\Connector\PDO\SQLite\DriverAdapter as SQLiteDriver;
use WebHemi\Data\DriverInterface as DataDriverInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemiTest\TestService\EmptyAuthStorage;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

/**
 * Class AclTest
 */
class AclTest extends TestCase
{
    /** @var array */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

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

        $databaseFile = realpath(__DIR__ . '/../../../build/webhemi_schema.sqlite3');

        self::$dataDriver = new SQLiteDriver('sqlite:' . $databaseFile);

        $fixture = realpath(__DIR__ . '/../TestData/sql/acl_test.sql');
        $setUpSql = file($fixture);

        if ($setUpSql) {
            foreach ($setUpSql as $sql) {
                $result = self::$dataDriver->query($sql);

                if (!$result) {
                    throw new IncompleteTestError(
                        'Cannot set up test database: '.json_encode(self::$dataDriver->errorInfo()).'; query: '.$sql
                    );
                }
            }
        }

        self::$adapter = new SQLiteAdapter(self::$dataDriver);
    }


        /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->config = require __DIR__ . '/../test_config.php';
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $userToPolicyProphecy = $this->prophesize(UserToPolicyCoupler::class);
        $userToPolicyCoupler = $userToPolicyProphecy->reveal();

        $userToGroupProphecy = $this->prophesize(UserToGroupCoupler::class);
        $userToGroupCoupler = $userToGroupProphecy->reveal();

        $userGroupToPolicyProphecy = $this->prophesize(UserGroupToPolicyCoupler::class);
        $userGroupToPolicyCoupler = $userGroupToPolicyProphecy->reveal();

        /**
         * @var UserToPolicyCoupler $userToPolicyCoupler
         * @var UserToGroupCoupler $userToGroupCoupler
         * @var UserGroupToPolicyCoupler $userGroupToPolicyCoupler
         */
        $aclAdapter = new Acl($userToPolicyCoupler, $userToGroupCoupler, $userGroupToPolicyCoupler);

        $this->assertInstanceOf(AclAdapterInterface::class, $aclAdapter);
    }

    /**
     * Tests access control validation with actual data.
     */
    public function testIsAllowed()
    {
        $userEntity = new UserEntity();
        $userGroupEntity = new UserGroupEntity();
        $policyEntity = new PolicyEntity();

        $resourceEntity = new ResourceEntity();
        $resourceEntity->setKeyData(90000);

        $applicationEntity = new ApplicationEntity();
        $applicationEntity->setKeyData(90000);

        $userA = clone $userEntity;
        $userA->setKeyData(90000);

        $userB = clone $userEntity;
        $userB->setKeyData(90001);

        $userToPolicyCoupler = new UserToPolicyCoupler(self::$adapter, $userEntity, $policyEntity);
        $userToGroupCoupler = new UserToGroupCoupler(self::$adapter, $userEntity, $userGroupEntity);
        $userGroupToPolicyCoupler = new UserGroupToPolicyCoupler(self::$adapter, $userGroupEntity, $policyEntity);

        /*
         * So the privileges are set as follows:
         *
         * Application ids: 90000
         * Resource ids:    90000
         * Policy ids:      90000, 90001, 90002, 90003
         * User Group ids:  90000, 90001
         * User ids:        90000, 90001
         *
         *     User A id 90000 is assigned to User Group id 90000        and to Policy id 90000, 90001
         *     User B id 90001 is assigned to User Group id        90001 and to Policy id                      90003
         *
         * User Group id 90000 is assigned                                   to Policy id 90000,               90003
         * User Group id 90001 is assigned                                   to Policy id        90001,        90003
         *
         * | Resource | Application |    | Policy || Allow User A? | Allow User B? |
         * +----------+-------------+----+--------++---------------+---------------+
         * |     null |        null | => |  90000 ||           Yes |            No |
         * |    90000 |        null | => |  90001 ||           Yes |           Yes |
         * |     null |       90000 | => |  90002 ||           Yes |            No |
         * |    90000 |       90000 | => |  90003 ||           Yes |           Yes |
         *
         * User A has the Jolly Joker policy (all resource in all applicatio), so it can access to anything.
         */

        $aclAdapter = new Acl($userToPolicyCoupler, $userToGroupCoupler, $userGroupToPolicyCoupler);

        $this->assertTrue($aclAdapter->isAllowed($userA, null, null));
        $this->assertFalse($aclAdapter->isAllowed($userB, null, null));

        $this->assertTrue($aclAdapter->isAllowed($userA, $resourceEntity, null));
        $this->assertTrue($aclAdapter->isAllowed($userB, $resourceEntity, null));

        $this->assertTrue($aclAdapter->isAllowed($userA, null, $applicationEntity));
        $this->assertFalse($aclAdapter->isAllowed($userB, null, $applicationEntity));

        $this->assertTrue($aclAdapter->isAllowed($userA, $resourceEntity, $applicationEntity));
        $this->assertTrue($aclAdapter->isAllowed($userB, $resourceEntity, $applicationEntity));
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $fixture = realpath(__DIR__.'/../TestData/sql/acl_test.rollback.sql');
        $tearDownSql = file($fixture);

        if ($tearDownSql) {
            foreach ($tearDownSql as $sql) {
                self::$dataDriver->query($sql);
            }
        }
    }
}
