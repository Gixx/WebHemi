<?php
/**
 * WebHemi
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Adapter\Router\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Router\Result;

/**
 * Class FastRouteAdapter.
 */
class FastRouteAdapter implements RouterAdapterInterface
{
    /** @var Result */
    private $result;
    /** @var Dispatcher\GroupCountBased */
    private $adapter;
    /** @var string */
    private $applicationPath;

    /**
     * FastRouteAdapter constructor.
     *
     * @param ConfigInterface    $configuration
     * @param EnvironmentManager $environmentManager
     * @param Result             $routeResult
     */
    public function __construct(
        ConfigInterface $configuration,
        EnvironmentManager $environmentManager,
        Result $routeResult
    ) {
        $module = $environmentManager->getSelectedModule();
        $routes = $configuration->getData('router/'.$module);

        $this->result = $routeResult;
        $this->applicationPath = $environmentManager->getSelectedApplicationUri();
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
     * @return string
     */
    private function getApplicationRouteUri(ServerRequestInterface $request) : string
    {
        $uri = $request->getUri()->getPath();

        if ($this->applicationPath != '/' && strpos($uri, $this->applicationPath) === 0) {
            $uri = substr($uri, strlen($this->applicationPath));
        }

        return empty($uri) ? '/' : $uri;
    }

    /**
     * Processes the Request and give a Result.
     *
     * @param ServerRequestInterface $request
     * @return Result
     */
    public function match(ServerRequestInterface $request) : Result
    {
        $httpMethod = $request->getMethod();
        $uri        = $this->getApplicationRouteUri($request);
        $routeInfo  = $this->adapter->dispatch($httpMethod, $uri);
        $result     = clone $this->result;

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $result->setStatus(Result::CODE_FOUND);
                $result->setMatchedMiddleware($routeInfo[1]);
                $result->setParameters($routeInfo[2]);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $result->setStatus(Result::CODE_BAD_METHOD);
                break;
            case Dispatcher::NOT_FOUND:
            default:
                $result->setStatus(Result::CODE_NOT_FOUND);
                break;
        }

        return $result;
    }
}
