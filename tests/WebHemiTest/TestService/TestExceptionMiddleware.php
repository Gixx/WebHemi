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
namespace WebHemiTest\TestService;

use InvalidArgumentException;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class TestExceptionMiddleware
 */
class TestExceptionMiddleware implements MiddlewareInterface
{
    /**
     * TestMiddleware constructor.
     */
    public function __construct()
    {
        throw new InvalidArgumentException('it should cause an error');
    }

    /**
     * Invokes the middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface&$response) : void
    {
        $method = $request->getMethod();
        $response = $response->withHeader('request-method', $method);
    }
}
