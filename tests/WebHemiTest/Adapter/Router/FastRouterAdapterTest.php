<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Adapter\Router;

use FastRoute\Dispatcher;
use WebHemi\Adapter\Http\GuzzleHttp\ServerRequest;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;
use WebHemi\Config\Config;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Routing\Result;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;

/**
 * Class FastRouterAdapterTest.
 */
class FastRouterAdapterTest extends TestCase
{
    /** @var Config */
    protected $routeConfig;
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

        $options = [
            'index' => [
                'path'            => '/',
                'middleware'      => 'SomeMiddleware',
                'allowed_methods' => ['GET','POST'],
            ],
            'login' => [
                'path'            => '/login',
                'middleware'      => 'SomeLoginMiddleware',
                'allowed_methods' => ['GET'],
            ],
            'auth' => [
                'path'            => '/login_auth',
                'middleware'      => 'SomeAuthMiddleware',
                'allowed_methods' => ['POST'],
            ],
        ];

        $this->routeConfig = new Config($options);
        $this->routeResult = new Result();
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig);

        $this->assertInstanceOf(RouterAdapterInterface::class, $adapterObj);
        $this->assertAttributeInstanceOf(Dispatcher::class, 'adapter', $adapterObj);
    }

    /**
     * Tests a private method for various cases.
     */
    public function testPrivateMethod()
    {
        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig);
        $request = new ServerRequest('GET', '/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);

        $this->assertEquals('/', $result);

        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig, '/');
        $request = new ServerRequest('GET', '/some/path/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);

        $this->assertEquals('/some/path/', $result);

        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig, '/some_application');
        $request = new ServerRequest('GET', '/some_application/some/path/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);

        $this->assertEquals('/some/path/', $result);

        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig, '/some_application');
        $request = new ServerRequest('GET', '/some_application/');
        $result = $this->invokePrivateMethod($adapterObj, 'getApplicationRouteUri', [$request]);

        $this->assertEquals('/', $result);
    }

    /**
     * Tests routing with default application.
     */
    public function testRouteMatchWithDefaultApplication()
    {
        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig);

        $request = new ServerRequest('GET', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/login_auth');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeAuthMiddleware', $result->getMatchedMiddleware());

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
        $adapterObj = new FastRouteAdapter($this->routeResult, $this->routeConfig, '/admin');

        $request = new ServerRequest('GET', '/admin/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/admin/');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/admin/login_auth');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_FOUND, $result->getStatus());
        $this->assertEquals('SomeAuthMiddleware', $result->getMatchedMiddleware());

        $request = new ServerRequest('POST', '/admin/login');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_BAD_METHOD, $result->getStatus());


        $request = new ServerRequest('POST', '/admin/some-non-existing-address');
        $result = $adapterObj->match($request);
        $this->assertEquals(Result::CODE_NOT_FOUND, $result->getStatus());
    }
}
