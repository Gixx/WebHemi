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
namespace WebHemiTest\Middleware;

use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\GuzzleHttp\ServerRequest;
use WebHemi\Adapter\Http\GuzzleHttp\Response;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemiTest\Fixtures\TestMiddleware;
use WebHemiTest\Fixtures\TestActionMiddleware;

/**
 * Class DispatcherAdapterTest.
 */
class DispatcherAdapterTest extends TestCase
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

        $streamProphecy = $this->prophesize(StreamInterface::class);
        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);

        $templateRendererProphecy->render(Argument::type('string'), Argument::type('array'))
            ->will(
                function () use ($streamProphecy) {
                    return $streamProphecy->reveal();
                }
            );

        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();

        $middleware = new DispatcherMiddleware($templateRenderer);

        /** @var ResponseInterface $result */
        $result = $middleware($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(Response::STATUS_PROCESSING, $result->getStatusCode());
    }

    /**
     * Tests middleware when no action middleware is given.
     */
    public function testMiddlewareNoActionGiven()
    {
        $request = new ServerRequest('GET', '/');
        $response = new Response(Response::STATUS_PROCESSING);

        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        $middleware = new DispatcherMiddleware($templateRenderer);

        $this->setExpectedException(RuntimeException::class);
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

        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        $middleware = new DispatcherMiddleware($templateRenderer);

        $this->setExpectedException(RuntimeException::class);
        $middleware($request, $response);
    }
}
