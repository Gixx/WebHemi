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

use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Routing\Result;

/**
 * Interface RouterAdapterInterface.
 */
interface RouterAdapterInterface
{
    /**
     * RouterAdapterInterface constructor.
     *
     * @param ConfigInterface    $configuration
     * @param EnvironmentManager $environmentManager
     * @param Result             $routeResult
     */
    public function __construct(
        ConfigInterface $configuration,
        EnvironmentManager $environmentManager,
        Result $routeResult
    );

    /**
     * Processes the Request and give a Result.
     *
     * @param ServerRequestInterface $request
     *
     * @return Result
     */
    public function match(ServerRequestInterface $request);
}
