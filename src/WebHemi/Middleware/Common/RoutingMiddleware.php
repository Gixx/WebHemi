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

namespace WebHemi\Middleware\Common;

use Exception;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Router\ServiceInterface as RouterInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Router\Result;

/**
 * Class RoutingMiddleware.
 */
class RoutingMiddleware implements MiddlewareInterface
{
    /** @var RouterInterface */
    private $routerAdapter;

    /**
     * RoutingMiddleware constructor.
     *
     * @param RouterInterface $routerAdapter
     */
    public function __construct(RouterInterface $routerAdapter)
    {
        $this->routerAdapter = $routerAdapter;
    }

    /**
     * From the request the middleware determines whether the requested URI is valid or not.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @throws Exception
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        $routeResult = $this->routerAdapter->match($request);

        if ($routeResult->getStatus() !== Result\Result::CODE_FOUND) {
            throw new Exception($routeResult->getStatusReason(), $routeResult->getStatus());
        } else {
            $request = $request
                ->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS,
                    $routeResult->getMatchedMiddleware()
                );
            $request = $request
                ->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_ROUTING_RESOURCE,
                    $routeResult->getResource()
                );
            $request = $request
                ->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_ROUTING_PARAMETERS,
                    $routeResult->getParameters()
                );
        }
    }
}
