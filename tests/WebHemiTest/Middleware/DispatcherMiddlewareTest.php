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

use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\StreamInterface;
use PHPUnit_Framework_TestCase as TestCase;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Middleware\DispatcherMiddleware;

/**
 * Class DispatcherAdapterTest.
 */
class DispatcherAdapterTest extends TestCase
{
    /**
     * Test middleware when all the injections work as expected.
     */
    public function testMiddleware()
    {
        $request = new ServerRequest('GET', '/');
        $request = $request->withAttribute('template', 'test')
            ->withAttribute('data', ['test' => 'test']);
        $response = new Response(102);

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
        $this->assertEquals(102, $result->getStatusCode());
    }
}
