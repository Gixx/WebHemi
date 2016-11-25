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
namespace WebHemi\Middleware;

use Exception;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Routing\Result;

/**
 * Class RoutingMiddleware.
 */
class RoutingMiddleware implements MiddlewareInterface
{
    /** @var RouterAdapterInterface */
    private $routerAdapter;

    /**
     * RoutingMiddleware constructor.
     *
     * @param RouterAdapterInterface $routerAdapter
     */
    public function __construct(RouterAdapterInterface $routerAdapter)
    {
        $this->routerAdapter = $routerAdapter;
    }

    /**
     * From the request .
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface $response)
    {
        $routeResult = $this->routerAdapter->match($request);

        if ($routeResult->getStatus() !== Result::CODE_FOUND) {
            $response = $response->withStatus($routeResult->getStatus());
            $request  = $request->withAttribute(
                ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION,
                new Exception($routeResult->getStatusReason())
            );
        } else {
            $request = $request
                ->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS,
                    $routeResult->getMatchedMiddleware()
                )
                ->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_ROUTING_PARAMETERS,
                    $routeResult->getParameters()
                );
        }

        return $response;
    }
}
