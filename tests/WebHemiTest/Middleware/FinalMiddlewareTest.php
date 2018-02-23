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
use WebHemi\Auth\ServiceInterface as AuthAdapterInterface;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Environment\ServiceAdapter\Base\ServiceAdapter as EnvironmentManager;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\ServerRequest;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\Response;
use WebHemi\Logger\ServiceInterface as LogAdapterInterface;
use WebHemi\Middleware\Common\FinalMiddleware;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use WebHemiTest\TestService\EmptyLogger;

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

        $authAdapterProphecy = $this->prophesize(AuthAdapterInterface::class);
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);

        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = new EmptyLogger(new Config([]), '');

        $middleware = new FinalMiddleware($authAdapter, $environmentManager, $logAdapter);

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
        $response = new Response(404);

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

        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = new EmptyLogger(new Config([]), '');

        $middleware = new FinalMiddleware($authAdapter, $environmentManager, $logAdapter);

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
        $response = new Response(404);

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

        /** @var AuthAdapterInterface $authAdapter */
        $authAdapter = $authAdapterProphecy->reveal();
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $environmentProphecy->reveal();
        /** @var LogAdapterInterface $logAdapter */
        $logAdapter = new EmptyLogger(new Config([]), '');

        $middleware = new FinalMiddleware($authAdapter, $environmentManager, $logAdapter);

        /** @var ResponseInterface $result */
        $middleware($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue($request->isXmlHttpRequest());
    }
}
