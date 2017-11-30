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

namespace WebHemi\Router\ServiceAdapter\Base;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Router\ProxyInterface;
use WebHemi\Router\Result\Result;
use WebHemi\Router\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var Result */
    private $result;
    /** @var array */
    private $routes;
    /** @var string */
    private $module;
    /** @var string */
    private $application;
    /** @var string */
    private $applicationPath;
    /** @var ProxyInterface */
    private $proxy;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     * @param Result                 $routeResult
     * @param null|ProxyInterface    $routerProxy
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        Result $routeResult,
        ? ProxyInterface $routerProxy = null
    ) {
        $module = $environmentManager->getSelectedModule();
        $this->routes = $configuration->getData('router/'.$module);
        $this->proxy = $routerProxy;
        $this->result = $routeResult;
        $this->module = $environmentManager->getSelectedModule();
        $this->application = $environmentManager->getSelectedApplication();
        $this->applicationPath = $environmentManager->getSelectedApplicationUri();
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

        $result = clone $this->result;
        $routeDefinition = $this->findRouteDefinition($uri);

        if (empty($routeDefinition)) {
            return $result->setStatus(Result::CODE_NOT_FOUND);
        }

        if (!in_array($httpMethod, $routeDefinition['allowed_methods'])) {
            return $result->setStatus(Result::CODE_BAD_METHOD);
        }

        $result->setParameters($routeDefinition['parameters'])
            ->setResource($routeDefinition['resource'])
            ->setMatchedMiddleware($routeDefinition['middleware']);

        // Check if we marked the middleware to be resolved by the proxy
        if ($routeDefinition['middleware'] === 'proxy' && $this->proxy instanceof ProxyInterface) {
            $this->proxy->resolveMiddleware($this->application, $result);
        }

        if (empty($result->getMatchedMiddleware())) {
            return $result->setStatus(Result::CODE_NOT_FOUND);
        }

        return $result->setStatus(Result::CODE_FOUND);
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
     * Searches definition and returns data is found. First find, first served.
     *
     * @param string $uri
     * @return array|null
     */
    private function findRouteDefinition(string $uri) : ? array
    {
        $routeDefinition = [];
        $matches = [];

        foreach ($this->routes as $resource => $routeData) {
            $pattern = '#'.$routeData['path'].'#';

            if (preg_match_all($pattern, $uri, $matches, PREG_SET_ORDER, 0)) {
                $parameters = [];

                foreach ($matches[0] as $index => $value) {
                    if (!is_numeric($index)) {
                        $parameters[$index] = $value;
                    }
                }

                if ($this->module == 'Website') {
                    $parameters['path'] = !empty($matches[0]['path']) ? $matches[0]['path'] : '/';
                    $parameters['basename'] = !empty($matches[0]['basename']) ? $matches[0]['basename'] : 'index.html';
                }

                $routeDefinition = [
                    'uri'             => $uri,
                    'middleware'      => $routeData['middleware'],
                    'parameters'      => $parameters,
                    'resource'        => $resource,
                    'allowed_methods' => $routeData['allowed_methods']
                ];

                break;
            }
        }
        return $routeDefinition;
    }
}
