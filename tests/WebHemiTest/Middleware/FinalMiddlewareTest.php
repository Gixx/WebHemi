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
namespace WebHemiTest\Middleware;

use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Http\GuzzleHttp\ServerRequest;
use WebHemi\Adapter\Http\GuzzleHttp\Response;
use WebHemi\Adapter\Log\LogAdapterInterface;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Middleware\FinalMiddleware;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;

/**
 * Class FinalMiddlewareTest.
 */
class FinalMiddlewareTest extends TestCase
{
    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Tests middleware with no error.
     */
    public function testMiddlewareNoError()
    {
        $output = 'Hello World!';
        $request = new ServerRequest('GET', '/');

        $body = \GuzzleHttp\Psr7\stream_for($output);
        $response = new Response(Response::STATUS_OK);
        $response = $response->withBody($body);

        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        $authAdapterProphecy = $this->prophesize(AuthAdapterInterface::class);
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $logAdapterPropehcy = $this->prophesize(LogAdapterInterface::class);
        $logAdapterPropehcy->log(Argument::any(), Argument::type('string'), Argument::type('array'))->will(
            function () {
                return;
            }
        );

        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = $logAdapterPropehcy->reveal();

        $middleware = new FinalMiddleware($templateRenderer, $authAdapter, $environmentManager, $logAdapter);

        /** @var ResponseInterface $result */
        $middleware($request, $response);

        $this->assertSame(Response::STATUS_OK, $response->getStatusCode());
        $this->assertFalse($request->isXmlHttpRequest());
    }

    /**
     * Tests middleware with error.
     */
    public function testMiddlewareErrorHandling()
    {
        $request = new ServerRequest('GET', '/');
        $body = \GuzzleHttp\Psr7\stream_for('');
        $response = new Response(404);

        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        $templateRendererProphecy->render(Argument::type('string'), Argument::type('array'))->willReturn($body);

        $authAdapterProphecy = $this->prophesize(AuthAdapterInterface::class);
        $authAdapterProphecy->hasIdentity()->willReturn(true);
        $authAdapterProphecy->getIdentity()->will(
            function () {
                $userEntity = new UserEntity();
                $userEntity->setEmail('php.unit.test@foo.org');
                return $userEntity;
            }
        );
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $environmentProphecy->getSelectedModule()->willReturn("admin");
        $environmentProphecy->getClientIp()->willReturn("127.0.0.1");
        $logAdapterPropehcy = $this->prophesize(LogAdapterInterface::class);

        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = $logAdapterPropehcy->reveal();

        $middleware = new FinalMiddleware($templateRenderer, $authAdapter, $environmentManager, $logAdapter);

        /** @var ResponseInterface $result */
        $middleware($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Tests Ajax request.
     */
    public function testAjax()
    {
        $request = new ServerRequest(
            'GET',
            '/',
            [],
            null,
            '1.1',
            ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
        );
        $templateData = ['test' => 'data'];
        $request = $request->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, $templateData);
        $body = \GuzzleHttp\Psr7\stream_for('');
        $response = new Response(404);

        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        $templateRendererProphecy->render(Argument::type('string'), Argument::type('array'))->willReturn($body);

        $authAdapterProphecy = $this->prophesize(AuthAdapterInterface::class);
        $authAdapterProphecy->hasIdentity()->willReturn(true);
        $authAdapterProphecy->getIdentity()->will(
            function () {
                $userEntity = new UserEntity();
                $userEntity->setEmail('php.unit.test@foo.org');
                return $userEntity;
            }
        );
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $environmentProphecy->getSelectedModule()->willReturn("admin");
        $environmentProphecy->getClientIp()->willReturn("127.0.0.1");
        $logAdapterPropehcy = $this->prophesize(LogAdapterInterface::class);

        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = $logAdapterPropehcy->reveal();

        $middleware = new FinalMiddleware($templateRenderer, $authAdapter, $environmentManager, $logAdapter);

        /** @var ResponseInterface $result */
        $middleware($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue($request->isXmlHttpRequest());
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['Some headerName','Some-HeaderName'],
            ['some header-name','Some-Header-Name'],
            ['some-header-Name','Some-Header-Name'],
            ['SomeHeaderName','SomeHeaderName'],
            ['Some_Header_Name','Some_Header_Name'],
        ];
    }

    /**
     * Test header filter.
     *
     * @param string $inputData
     * @param string $expectedResult
     *
     * @dataProvider dataProvider
     */
    public function testFilterHeaderName($inputData, $expectedResult)
    {
        $templateRendererProphecy = $this->prophesize(RendererAdapterInterface::class);
        $authAdapterProphecy = $this->prophesize(AuthAdapterInterface::class);
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $logAdapterPropehcy = $this->prophesize(LogAdapterInterface::class);

        /** @var RendererAdapterInterface $templateRenderer */
        $templateRenderer = $templateRendererProphecy->reveal();
        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = $logAdapterPropehcy->reveal();

        $middleware = new FinalMiddleware($templateRenderer, $authAdapter, $environmentManager, $logAdapter);

        $actualResult = $this->invokePrivateMethod($middleware, 'filterHeaderName', [$inputData]);

        $this->assertSame($expectedResult, $actualResult);
    }
}
