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
namespace WebHemi\Middleware\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;

class FakeAction implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        $request = $request->withAttribute('template', 'no')
            ->withAttribute('data', ['<h2>This is the test action, buddy!</h2>']);

        return $response;
    }
}
