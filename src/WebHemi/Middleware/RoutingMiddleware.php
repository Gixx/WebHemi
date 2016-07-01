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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;

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
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        $routeResult = $this->routerAdapter->match($request);

        $request = $request
            ->withAttribute('routeResult', $routeResult)
            ->withAttribute('resolvedActionMiddleware', \WebHemi\Middleware\Action\FakeAction::class);

        return $response;
    }
}
