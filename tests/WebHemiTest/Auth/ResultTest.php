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

use Prophecy\Argument;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Auth\Result\Result;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemiTest\TestService\EmptyAuthAdapter;
use WebHemiTest\TestService\EmptyAuthStorage;
use WebHemiTest\TestService\EmptyCredential;
use WebHemiTest\TestService\EmptyUserStorage;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTest
 */
class ResultTest extends TestCase
{
    /** @var array */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

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
     * Tests auth adapter Result
     *
     * @covers \WebHemi\Auth\Result\Result
     */
    public function testResult()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $config = new Config($this->config);
        $result = new Result();
        $authStorage = new EmptyAuthStorage();
        $dataEntity = new UserEntity();
        $dataStorage = new EmptyUserStorage($defaultAdapterInstance, $dataEntity);

        $adapter = new EmptyAuthAdapter(
            $config,
            $result,
            $authStorage,
            $dataStorage
        );

        $emptyCredential = new EmptyCredential();


        $emptyCredential->setCredential('authResultShouldBe', Result::SUCCESS);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertTrue($result->isValid());
        $this->assertSame(Result::SUCCESS, $result->getCode());

        $emptyCredential->setCredential('authResultShouldBe', Result::FAILURE);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE, $result->getCode());

        // set it to a non-valid result code
        $emptyCredential->setCredential('authResultShouldBe', -100);
        $result = $adapter->authenticate($emptyCredential);
        $this->assertFalse($result->isValid());
        $this->assertSame(Result::FAILURE_OTHER, $result->getCode());
    }
}
