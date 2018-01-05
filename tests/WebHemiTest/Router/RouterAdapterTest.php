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
namespace WebHemiTest\Adapter\Router;

use PHPUnit\Framework\TestCase;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\ServerRequest;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Router\ServiceInterface as RouterAdapterInterface;
use WebHemi\Router\ServiceAdapter\Base\ServiceAdapter as RouteAdapter;
use WebHemi\Router\Result\Result;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use WebHemiTest\TestService\EmptyEnvironmentManager;
use WebHemiTest\TestService\EmptyRouteProxy;

/**
 * Class RouterAdapterTest.
 */
class RouterAdapterTest extends TestCase
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
    /** @var EmptyRouteProxy */
    protected $routeProxy;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../test_config.php';
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
        $this->routeProxy = new EmptyRouteProxy();
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
        );

        $this->assertInstanceOf(RouterAdapterInterface::class, $adapterObj);
    }

    /**
     * Tests a private method for various cases.
     */
    public function testPrivateMethod()
    {
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
        );
        $request = new ServerRequest('GET', '/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/', $result);

        $request = new ServerRequest('GET', '/some/path/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);
        $this->assertEquals('/some/path/', $result);

        // Change application root
        $this->environmentManager->setSelectedApplicationUri('/some_application');
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
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
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
        );

        $request = new ServerRequest('GET', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('actionOk', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('actionOk', $result->getMatchedMiddleware());

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
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
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

    /**
     * Tests routing with default application.
     */
    public function testRouteMatchWithProxy()
    {
        $adapterObj = new RouteAdapter(
            $this->config,
            $this->environmentManager,
            $this->routeResult,
            $this->routeProxy
        );

        $request = new ServerRequest('GET', '/proxytest/test/level/1/actionok.html');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('ActionOK', $result->getMatchedMiddleware());
        $this->assertEquals('proxy-view-test', $result->getResource());

        $request = new ServerRequest('GET', '/proxytest/some/sub/directory/actionbad.html');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('ActionBad', $result->getMatchedMiddleware());
        $this->assertEquals('proxy-view-test', $result->getResource());

        $request = new ServerRequest('GET', '/proxytest/some/sub/directory/non_existing.html');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_NOT_FOUND, $result->getStatus());
        $this->assertEquals('proxy-view-test', $result->getResource());

        // Test if a directory listing is denied
        $request = new ServerRequest('GET', '/proxytest/some/sub/directory');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FORBIDDEN, $result->getStatus());
        $this->assertEquals('proxy-list-test', $result->getResource());

        $request = new ServerRequest('GET', '/proxytest/some/sub/directory/index.html');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FORBIDDEN, $result->getStatus());
        $this->assertEquals('proxy-list-test', $result->getResource());
    }
}
