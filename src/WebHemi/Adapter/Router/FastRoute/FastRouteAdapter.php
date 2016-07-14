<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\Adapter\Router\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Config\ConfigInterface;
use WebHemi\Routing\Result;

/**
 * Class FastRouteAdapter.
 */
class FastRouteAdapter implements RouterAdapterInterface
{
    /** @var Result */
    private $result;
    /** @var ConfigInterface */
    private $config;
    /** @var Dispatcher */
    private $adapter;
    /** @var string */
    private $applicationPath;

    /**
     * FastRouteAdapter constructor.
     *
     * @param Result          $routeResult
     * @param ConfigInterface $routeConfig
     * @param string          $applicationPath
     */
    public function __construct(Result $routeResult, ConfigInterface $routeConfig, $applicationPath = '/')
    {
        $this->result = $routeResult;
        $this->config = $routeConfig;
        $this->applicationPath = $applicationPath;

        $routes = $this->config->toArray();

        /** @var Dispatcher\GroupCountBase adapter */
        $this->adapter = \FastRoute\simpleDispatcher(
            function (RouteCollector $routeCollector) use ($routes) {
                foreach ($routes as $route) {
                    $method   = $route['allowed_methods'];
                    $uri      = $route['path'];
                    $callback = $route['middleware'];
                    $routeCollector->addRoute($method, $uri, $callback);
                }
            }
        );
    }

    /**
     * According to the application path, determines the route uri
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getApplicationRouteUri(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();

        if ($this->applicationPath != '/' && strpos($uri, $this->applicationPath) === 0) {
            $uri = substr($uri, strlen($this->applicationPath));
        }

        return $uri;
    }

    /**
     * Processes the Request and give a Result.
     *
     * @param ServerRequestInterface $request
     *
     * @return Result
     */
    public function match(ServerRequestInterface $request)
    {
        $httpMethod = $request->getMethod();
        $uri        = $this->getApplicationRouteUri($request);
        $routeInfo  = $this->adapter->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $this->result->setStatus(Result::CODE_FOUND);
                $this->result->setMatchedMiddleware($routeInfo[1]);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->result->setStatus(Result::CODE_BAD_METHOD);
                break;
            case Dispatcher::NOT_FOUND:
            default:
                $this->result->setStatus(Result::CODE_NOT_FOUND);
                break;
        }

        return $this->result;
    }
}
