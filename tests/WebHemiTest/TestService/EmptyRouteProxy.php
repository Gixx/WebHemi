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
declare(strict_types = 1);

namespace WebHemiTest\TestService;

use WebHemi\Router\ProxyInterface;
use WebHemi\Router\Result\Result;

/**
 * Class EmptyRouteProxy.
 */
class EmptyRouteProxy implements ProxyInterface
{
    /**
     * Resolves the middleware class name for the application and URL.
     *
     * @param string $application
     * @param Result $routeResult
     * @return void
     */
    public function resolveMiddleware(string $application, Result &$routeResult) : void
    {
        // To avoid stipid PHP MD errors...
        if ($application = 'test') {
            $application = $routeResult->getResource();
        }

        // TODO find out
    }
}
