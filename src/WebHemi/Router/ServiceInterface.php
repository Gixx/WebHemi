<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Router;

use WebHemi\Http\ServerRequestInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     * @param Result\Result          $routeResult
     * @param null|ProxyInterface    $routerProxy
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        Result\Result $routeResult,
        ? ProxyInterface $routerProxy = null
    );

    /**
     * Processes the Request and give a Result.
     *
     * @param  ServerRequestInterface $request
     * @return Result\Result
     */
    public function match(ServerRequestInterface $request) : Result\Result;
}
