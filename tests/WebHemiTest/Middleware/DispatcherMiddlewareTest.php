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
namespace WebHemiTest\Middleware;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\ServerRequest;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\Response;
use WebHemi\Middleware\Common\DispatcherMiddleware;
use WebHemiTest\TestService\TestMiddleware;
use WebHemiTest\TestService\TestActionMiddleware;

/**
 * Class DispatcherMiddlewareTest.
 */
class DispatcherMiddlewareTest extends TestCase
{
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
     * Tests middleware when all the injections work as expected.
     */
    public function testMiddleware()
    {
        $middlewareAction = new TestActionMiddleware();

        $request = new ServerRequest('GET', '/');
        $request = $request
            ->withAttribute(ServerRequest::REQUEST_ATTR_ACTION_MIDDLEWARE, $middlewareAction)
            ->withAttribute(ServerRequest::REQUEST_ATTR_DISPATCH_TEMPLATE, 'test')
            ->withAttribute(ServerRequest::REQUEST_ATTR_DISPATCH_DATA, ['test' => 'test']);
        $response = new Response(Response::STATUS_PROCESSING);

        $middleware = new DispatcherMiddleware();
        $responseBeforeMiddleware = $response;
        $this->assertTrue($response === $responseBeforeMiddleware);

        $middleware($request, $response);
        $this->assertTrue($response !== $responseBeforeMiddleware);
        $this->assertEquals(Response::STATUS_PROCESSING, $response->getStatusCode());
    }

    /**
     * Tests middleware when no action middleware is given.
     */
    public function testMiddlewareNoActionGiven()
    {
        $request = new ServerRequest('GET', '/');
        $response = new Response(Response::STATUS_PROCESSING);

        $middleware = new DispatcherMiddleware();

        $this->expectException(RuntimeException::class);
        $middleware($request, $response);
    }

    /**
     * Tests middleware when given object is not an action middleware.
     */
    public function testMiddlewareObjectIsNotAction()
    {
        $middlewareAction = new TestMiddleware('x');

        $request = new ServerRequest('GET', '/');
        $request = $request->withAttribute(ServerRequest::REQUEST_ATTR_ACTION_MIDDLEWARE, $middlewareAction);
        $response = new Response(Response::STATUS_PROCESSING);

        $middleware = new DispatcherMiddleware();

        $this->expectException(RuntimeException::class);
        $middleware($request, $response);
    }
}
