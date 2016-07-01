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
namespace WebHemi\Adapter\Router;

use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Routing\Result;

/**
 * Class FakeRouter
 */
class FakeRouter implements RouterAdapterInterface
{
    /**
     * Processes the Request and give a Result.
     *
     * @param ServerRequestInterface $request
     *
     * @return Result
     */
    public function match(ServerRequestInterface $request)
    {
        $routeResult = new Result();
        $routeResult->request = $request;

        return $routeResult;
    }
}
