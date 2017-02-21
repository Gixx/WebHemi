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

namespace WebHemiTest\Auth;

use PDO;
use WebHemi\Auth\Auth;
use WebHemi\Auth\Result;
use WebHemi\Auth\Credential\NameAndPasswordCredential;
use WebHemi\Config\Config;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Adapter\Data\PDO\SQLiteAdapter;
use WebHemi\Adapter\Data\PDO\SQLiteDriver;
use WebHemi\Adapter\Data\DataDriverInterface;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemiTest\Fixtures\EmptyAuthStorage;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_IncompleteTestError;
use PHPUnit_Framework_SkippedTestError;

/**
 * Class AuthTest
 */
class AuthTest extends TestCase
{
    /** @var array */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /** @var DataDriverInterface */
    protected static $dataDriver;
    /** @var SQLiteAdapter */
    protected static $adapter;

    const TEST_PASSWORD = 'test_password';

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

        $databaseFile = realpath(__DIR__ . '/../../../build/webhemi_schema.sqlite3');

        self::$dataDriver = new SQLiteDriver('sqlite:' . $databaseFile);

        $fixture = realpath(__DIR__.'/../Fixtures/sql/authentication_test.sql');
        $setUpSql = file($fixture);

        if ($setUpSql) {
            foreach ($setUpSql as $sql) {
                $result = self::$dataDriver->query($sql);

                if (!$result) {
                    throw new PHPUnit_Framework_IncompleteTestError(
                        'Cannot set up test database: '.json_encode(self::$dataDriver->errorInfo()).'; query: '.$sql
                    );
                }
            }
        }

        // since the password is hashed in PHP, it's needed to update the test data in the test database.
        $testPassword = password_hash(self::TEST_PASSWORD, PASSWORD_DEFAULT);
        $sql = 'UPDATE webhemi_user SET password = ? WHERE id_user IN (90000, 90001, 90002)';
        $statement = self::$dataDriver->prepare($sql);
        $statement->bindValue(1, $testPassword, PDO::PARAM_STR);
        $result = $statement->execute();

        if (!$result) {
            throw new PHPUnit_Framework_IncompleteTestError(
                'Cannot set up test database: '.json_encode(self::$dataDriver->errorInfo()).'; query: '.$sql
            );
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

        $this->config = require __DIR__ . '/../Fixtures/test_config.php';
    }

    /**
     * Tests authentication
     *
     * @covers \WebHemi\Auth\Auth
     */
    public function testAuthenticate()
    {
        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $dataStorage = new UserStorage(self::$adapter, $dataEntity);

        $adapter = new Auth(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $credential = new NameAndPasswordCredential();
        $credential->addCredential('username', 'test');
        $credential->addCredential('password', 'test');

        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->addCredential('username', 'test_user_1');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_DISABLED, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->addCredential('username', 'test_user_2');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_INACTIVE, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->addCredential('username', 'test_user_3');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->addCredential('password', self::TEST_PASSWORD);
        $result = $adapter->authenticate($credential);
        $this->assertTrue($result->isValid());
        $this->assertSame(Result::SUCCESS, $result->getCode());
        $this->assertTrue($adapter->hasIdentity());
        $this->assertInstanceOf(UserEntity::class, $adapter->getIdentity());
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $fixture = realpath(__DIR__.'/../Fixtures/sql/authentication_test.rollback.sql');
        $tearDownSql = file($fixture);

        if ($tearDownSql) {
            foreach ($tearDownSql as $sql) {
                self::$dataDriver->query($sql);
            }
        }
    }
}
