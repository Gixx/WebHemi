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

use PHPUnit_Framework_TestCase as TestCase;
use WebHemi\Adapter\DependencyInjection\Symfony\SymfonyAdapter as DependencyInjectionAdapter;
use WebHemi\Application\ApplicationInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Application\PipelineManager;
use WebHemi\Application\SessionManager;
use WebHemi\Application\Web\WebApplication as Application;
use WebHemi\Config\Config;
use WebHemi\Config\ConfigInterface;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use WebHemiTest\Fixtures\EmptyEnvironmentManager;
use WebHemiTest\Fixtures\TestMiddleware;

/**
 * Class WebapplicationTest.
 */
class WebApplicationTest extends TestCase
{
    /** @var array */
    protected $config;
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

    use AssertTrait;
    use InvokePrivateMethodTrait;

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
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        TestMiddleware::$counter = 0;
        TestMiddleware::$trace = [];
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $config = new Config($this->config);
        $environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $pipelineManager = new PipelineManager($config);

        $diAdapter = new DependencyInjectionAdapter($config);
        $diAdapter->registerService(ConfigInterface::class, $config)
            ->registerService(EnvironmentManager::class, $environmentManager)
            ->registerService(PipelineManager::class, $pipelineManager)
            ->registerModuleServices('Global');

        $app = new Application($diAdapter);

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertTrue($diAdapter === $app->getContainer());
        $actualObject = $app->getContainer()->get(EnvironmentManager::class);
        $this->assertInstanceOf(EnvironmentManager::class, $actualObject);
        $this->assertTrue($environmentManager === $actualObject);
    }

    /**
     * Test run with no error.
     */
    public function testRun()
    {
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $config = new Config($this->config);
        $environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $pipelineManager = new PipelineManager($config);

        $diAdapter = new DependencyInjectionAdapter($config);
        $diAdapter->registerService(ConfigInterface::class, $config)
            ->registerService(EnvironmentManager::class, $environmentManager)
            ->registerService(PipelineManager::class, $pipelineManager)
            ->registerModuleServices('Global');

        $app = new Application($diAdapter);
        $app->run();

        $expectedPipelineTrace = [
            'pipe2',
            'pipe3',
            'pipe1',
            'pipe4',
            'final'
        ];

        $this->assertSame(count($expectedPipelineTrace), TestMiddleware::$counter);
        $this->assertArraysAreSimilar($expectedPipelineTrace, TestMiddleware::$trace);
        $this->assertSame(200, TestMiddleware::$responseStatus);

        $expectedBody = [
            'message' => 'Hello World!',
            'template_resource_path' => '/resources/vendor_themes/test_theme/static'
        ];
        $actualBody = json_decode(TestMiddleware::$responseBody, true);
        $this->assertArraysAreSimilar($expectedBody, $actualBody);
    }

    /**
     * Test run with error.
     */
    public function testRunError()
    {
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/error/',
            'QUERY_STRING' => '',
        ];

        $config = new Config($this->config);
        $environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $pipelineManager = new PipelineManager($config);

        $diAdapter = new DependencyInjectionAdapter($config);
        $diAdapter->registerService(ConfigInterface::class, $config)
            ->registerService(EnvironmentManager::class, $environmentManager)
            ->registerService(PipelineManager::class, $pipelineManager)
            ->registerModuleServices('Global');

        $app = new Application($diAdapter);
        $app->run();

        $expectedPipelineTrace = [
            'pipe2',
            'pipe3',
            'pipe1',
            'final'
        ];

        $this->assertSame(count($expectedPipelineTrace), TestMiddleware::$counter);
        $this->assertArraysAreSimilar($expectedPipelineTrace, TestMiddleware::$trace);
        $this->assertSame(500, TestMiddleware::$responseStatus);
        $this->assertEmpty(TestMiddleware::$responseBody);
    }

    /**
     * Test run with error 2.
     */
    public function testRunForNonExistsPage()
    {
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/page-not-exists/',
            'QUERY_STRING' => '',
        ];

        $config = new Config($this->config);
        $environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $pipelineManager = new PipelineManager($config);

        $diAdapter = new DependencyInjectionAdapter($config);
        $diAdapter->registerService(ConfigInterface::class, $config)
            ->registerService(EnvironmentManager::class, $environmentManager)
            ->registerService(PipelineManager::class, $pipelineManager)
            ->registerModuleServices('Global');

        $app = new Application($diAdapter);
        $app->run();

        $expectedPipelineTrace = [
            'pipe2',
            'final'
        ];

        $this->assertSame(count($expectedPipelineTrace), TestMiddleware::$counter);
        $this->assertArraysAreSimilar($expectedPipelineTrace, TestMiddleware::$trace);
        $this->assertSame(404, TestMiddleware::$responseStatus);
        $this->assertEmpty(TestMiddleware::$responseBody);
    }

    /**
     * Test run with bad method request.
     */
    public function testRunForBadMethod()
    {
        $this->server = [
            'HTTP_HOST'      => 'unittest.dev',
            'SERVER_NAME'    => 'unittest.dev',
            'REQUEST_URI'    => '/login',
            'REQUEST_METHOD' => 'POST',
            'QUERY_STRING'   => '',
        ];

        $config = new Config($this->config);
        $environmentManager = new EmptyEnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $pipelineManager = new PipelineManager($config);

        $diAdapter = new DependencyInjectionAdapter($config);
        $diAdapter->registerService(ConfigInterface::class, $config)
            ->registerService(EnvironmentManager::class, $environmentManager)
            ->registerService(PipelineManager::class, $pipelineManager)
            ->registerModuleServices('Global');

        $app = new Application($diAdapter);
        $app->run();

        $expectedPipelineTrace = [
            'pipe2',
            'final'
        ];

        $this->assertSame(count($expectedPipelineTrace), TestMiddleware::$counter);
        $this->assertArraysAreSimilar($expectedPipelineTrace, TestMiddleware::$trace);
        $this->assertSame(405, TestMiddleware::$responseStatus);
        $this->assertEmpty(TestMiddleware::$responseBody);
    }
}
