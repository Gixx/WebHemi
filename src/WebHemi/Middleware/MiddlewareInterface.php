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
declare(strict_types=1);

namespace WebHemi\Middleware;

use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;

/**
 * Interface MiddlewareInterface.
 */
interface MiddlewareInterface
{
    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void;
}
