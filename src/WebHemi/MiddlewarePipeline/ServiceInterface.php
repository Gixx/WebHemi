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

namespace WebHemi\MiddlewarePipeline;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration);

    /**
     * Adds module specific pipeline.
     *
     * @param string $moduleName
     * @return ServiceInterface
     */
    public function addModulePipeLine(string $moduleName) : ServiceInterface;

    /**
     * Adds a new middleware to the pipeline queue.
     *
     * @param string $middleWareClass
     * @param int    $priority
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function queueMiddleware(string $middleWareClass, int $priority = 50) : ServiceInterface;

    /**
     * Starts the pipeline.
     *
     * @return null|string
     */
    public function start() : ? string;

    /**
     * Gets next element from the pipeline.
     *
     * @return null|string
     */
    public function next() : ? string;

    /**
     * Gets the full pipeline list.
     *
     * @return array
     */
    public function getPipelineList() : array;
}
