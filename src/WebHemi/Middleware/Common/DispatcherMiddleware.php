<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Common;

use RuntimeException;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\ActionMiddlewareInterface;

/**
 * Class DispatcherMiddleware.
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * From the request data renders an output for the response, or sets an error status code.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @throws RuntimeException
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        /**
         * @var MiddlewareInterface $actionMiddleware
         */
        $actionMiddleware = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE);

        // If there is a valid action Middleware, then dispatch it.
        if ($actionMiddleware instanceof ActionMiddlewareInterface) {
            /**
             * @var ResponseInterface $response
             */
            $actionMiddleware($request, $response);
        } else {
            throw new RuntimeException(sprintf('The given attribute is not a valid Action Middleware.'), 1000);
        }
    }
}
