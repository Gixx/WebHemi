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
namespace WebHemiTest\Adapter\Router;

use FastRoute\Dispatcher;
use WebHemi\Adapter\Http\GuzzleHttp\ServerRequest;
use PHPUnit_Framework_TestCase as TestCase;
use WebHemi\Config\Config;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Routing\Result;
use WebHemiTest\AssertTrait;
use WebHemiTest\Fixtures\EmptyEnvironmentManager;
use WebHemiTest\InvokePrivateMethodTrait;

/**
 * Class FastRouterAdapterTest.
 */
class FastRouterAdapterTest extends TestCase
{
    /** @var Config */
    protected $config = [];
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
    /** @var Result */
    protected $routeResult;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../Fixtures/test_config.php';
        $this->config = new Config($config);
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $this->environmentManager = new EmptyEnvironmentManager(
            $this->config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
        $this->routeResult = new Result();
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $adapterObj = new FastRouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult
        );

        $this->assertInstanceOf(RouterAdapterInterface::class, $adapterObj);
        $this->assertAttributeInstanceOf(Dispatcher::class, 'adapter', $adapterObj);
    }

    /**
     * Tests a private method for various cases.
     */
    public function testPrivateMethod()
    {
        $adapterObj = new FastRouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult
        );
        $request = new ServerRequest('GET', '/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/', $result);

        $request = new ServerRequest('GET', '/some/path/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/some/path/', $result);

        // Change application root
        $this->environmentManager->setSelectedApplicationUri('/some_application');
        $adapterObj = new FastRouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult
        );
        $request = new ServerRequest('GET', '/some_application/some/path/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/some/path/', $result);

        $request = new ServerRequest('GET', '/some_application/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/', $result);
    }

    /**
     * Tests routing with default application.
     */
    public function testRouteMatchWithDefaultApplication()
    {
        $adapterObj = new FastRouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult
        );

        $request = new ServerRequest('GET', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('ActionOK', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('ActionOK', $result->getMatchedMiddleware());

        $request = new ServerRequest('GET', '/login');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeLoginMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/login');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_BAD_METHOD, $result->getStatus());


        $request = new ServerRequest('POST', '/some-non-existing-address');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_NOT_FOUND, $result->getStatus());
    }

    /**
     * Tests routing with not the default application.
     */
    public function testRouteMatchWithNonDefaultApplication()
    {
        $this->environmentManager->setSelectedModule('SomeApp')
            ->setSelectedApplicationUri('/some_application')
            ->setSelectedApplication('some_app');
        $adapterObj = new FastRouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult
        );

        $request = new ServerRequest('GET', '/some_application/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeIndexMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/some_application/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeIndexMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('GET', '/some_application/some/path');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeOtherMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/some_application/some/path');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_BAD_METHOD, $result->getStatus());


        $request = new ServerRequest('POST', '/some_application/some-non-existing-address');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_NOT_FOUND, $result->getStatus());
    }
}
