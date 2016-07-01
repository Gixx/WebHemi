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
namespace WebHemi\Middleware\Pipeline;

/**
 * Interface MiddlewarePipelineInterface.
 */
interface MiddlewarePipelineInterface
{
    /**
     * Adds a new middleware to the pipeline queue.
     *
     * @param string $middleWareClass
     * @param int    $priority
     *
     * @return $this
     */
    public function queueMiddleware($middleWareClass, $priority = 50);

    /**
     * Starts the pipeline.
     *
     * @return null|string
     */
    public function start();

    /**
     * Gets next element from the pipeline.
     *
     * @return null|string
     */
    public function next();
}
