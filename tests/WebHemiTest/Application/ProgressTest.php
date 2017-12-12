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
namespace WebHemiTest\Application;

use PHPUnit\Framework\TestCase;
use WebHemi\Application\Progress;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use WebHemiTest\TestService\EmptyEnvironmentManager;
use WebHemiTest\TestService\EmptySessionManager;

/**
 * Class ProgressTest
 */
class ProgressTest extends TestCase
{
    /** @var array */
    protected $get = [];
    /** @var array */
    protected $post = [];
    /** @var array */
    protected $server;
    /** @var array */
    protected $cookie = [];
    /** @var array */
    protected $files = [];
    /** @var EmptyEnvironmentManager */
    protected $environmentManager;
    /** @var EmptySessionManager */
    protected $sessionManager;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $configData = require __DIR__ . '/../test_config.php';

        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $config = new Config($configData);
        $this->environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $this->sessionManager = new EmptySessionManager($config);
        $this->sessionManager->start('unittest');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        $path = $this->environmentManager->getApplicationRoot().'/data/progress/*.json';
        $files = glob($path);

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $expectedSessionId = $this->sessionManager->getSessionId();

        $progress = new Progress($this->environmentManager, $this->sessionManager);

        $this->assertAttributeInstanceOf(EnvironmentInterface::class, 'environmentManager', $progress);
        $this->assertAttributeEquals($expectedSessionId, 'sessionId', $progress);
    }

    /*
     * Tests start() method with no specific name given.
     */
    public function testStartWithNoCallerName()
    {
        $progress = new Progress($this->environmentManager, $this->sessionManager);
        $progress->start(3);

        $expectedProgressId = md5($this->sessionManager->getSessionId()).'_ProgressTest';
        $expectedProgressFile = $this->environmentManager->getApplicationRoot()
            .'/data/progress/'.$expectedProgressId.'.json';

        $this->assertAttributeEquals('ProgressTest', 'callerName', $progress);
        $this->assertAttributeEquals(3, 'totalSteps', $progress);
        $this->assertAttributeEquals(1, 'currentStep', $progress);
        $this->assertAttributeEquals($expectedProgressId, 'progressId', $progress);
        $this->assertEquals($expectedProgressId, $progress->getProgressId());
        $this->assertTrue(file_exists($expectedProgressFile));
    }

    /*
     * Tests start() method with specific name given.
     */
    public function testStartWithCallerName()
    {
        $progress = new Progress($this->environmentManager, $this->sessionManager);
        $progress->start(3, 'ProgressTestInDaHouse');

        $expectedProgressId = md5($this->sessionManager->getSessionId()).'_ProgressTestInDaHouse';
        $expectedProgressFile = $this->environmentManager->getApplicationRoot()
            .'/data/progress/'.$expectedProgressId.'.json';

        $this->assertAttributeEquals('ProgressTestInDaHouse', 'callerName', $progress);
        $this->assertAttributeEquals(3, 'totalSteps', $progress);
        $this->assertAttributeEquals(1, 'currentStep', $progress);
        $this->assertAttributeEquals($expectedProgressId, 'progressId', $progress);
        $this->assertEquals($expectedProgressId, $progress->getProgressId());
        $this->assertTrue(file_exists($expectedProgressFile));
    }

    /**
     * Tests the next() method.
     */
    public function testNext()
    {
        $progress = new Progress($this->environmentManager, $this->sessionManager);
        $progress->start(3);

        $this->assertAttributeEquals(3, 'totalSteps', $progress);
        $this->assertAttributeEquals(1, 'currentStep', $progress);

        $progress->next();
        $this->assertAttributeEquals(2, 'currentStep', $progress);

        $progress->next();
        $this->assertAttributeEquals(3, 'currentStep', $progress);
    }
}
