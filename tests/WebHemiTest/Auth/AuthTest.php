<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\Auth;

use PDO;
use WebHemi\Auth\ServiceAdapter\Base\ServiceAdapter as Auth;
use WebHemi\Auth\Result\Result;
use WebHemi\Auth\Credential\NameAndPasswordCredential;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Data\Query\SQLite\QueryAdapter as SQLiteAdapter;
use WebHemi\Data\Driver\PDO\SQLite\DriverAdapter as SQLiteDriver;
use WebHemi\Data\Storage\UserStorage;
use WebHemiTest\TestService\EmptyAuthStorage;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedTestError;

/**
 * Class AuthTest
 */
class AuthTest extends TestCase
{
    /** @var array */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /** @var PDO */
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

        $fixture = realpath(__DIR__ . '/../TestData/sql/authentication_test.sql');
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

        // since the password is hashed in PHP, it's needed to update the test data in the test database.
        $testPassword = password_hash(self::TEST_PASSWORD, PASSWORD_DEFAULT);
        $sql = 'UPDATE webhemi_user SET password = ? WHERE id_user IN (90000, 90001, 90002)';
        $statement = self::$dataDriver->prepare($sql);
        $statement->bindValue(1, $testPassword, PDO::PARAM_STR);
        $result = $statement->execute();

        if (!$result) {
            throw new IncompleteTestError(
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

        $this->config = require __DIR__ . '/../test_config.php';
    }

    /**
     * Tests authentication
     *
     * @covers \WebHemi\Auth\ServiceAdapter\Base\ServiceAdapter
     */
    public function testAuthenticate()
    {
        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $entitySet = new EntitySet();
        $dataStorage = new UserStorage(self::$adapter, $entitySet, $dataEntity);

        $adapter = new Auth(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $credential = new NameAndPasswordCredential();
        $credential->setCredential('username', 'test');
        $credential->setCredential('password', 'test');

        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->setCredential('username', 'test_user_1');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_DISABLED, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->setCredential('username', 'test_user_2');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_IDENTITY_INACTIVE, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->setCredential('username', 'test_user_3');
        $result = $adapter->authenticate($credential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertFalse($adapter->hasIdentity());

        $credential->setCredential('password', self::TEST_PASSWORD);
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
        $fixture = realpath(__DIR__ . '/../TestData/sql/authentication_test.rollback.sql');
        $tearDownSql = file($fixture);

        if ($tearDownSql) {
            foreach ($tearDownSql as $sql) {
                self::$dataDriver->query($sql);
            }
        }
    }
}
