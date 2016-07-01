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

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\RoutingMiddleware;

/**
 * Class Pipeline.
 */
class Pipeline implements MiddlewarePipelineInterface
{
    /** @var array */
    private $priorityList;
    /** @var array */
    private $pipelineList;
    /** @var array */
    private $keyMiddlewareList;
    /** @var int */
    private $index;

    /**
     * Pipeline constructor.
     */
    public function __construct()
    {
        $this->keyMiddlewareList = [
            RoutingMiddleware::class,
            DispatcherMiddleware::class,
            FinalMiddleware::class
        ];

        // The FinalMiddleware should not be part of the queue.
        $this->priorityList = [
            0   => [RoutingMiddleware::class],
            100 => [DispatcherMiddleware::class]
        ];

        $this->pipelineList = [
            RoutingMiddleware::class,
            DispatcherMiddleware::class,
        ];
    }

    /**
     * Adds a new middleware to the pipeline queue.
     *
     * @param string $middleWareClass
     * @param int    $priority
     *
     * @throws RuntimeException
     *
     * @return $this
     */
    public function queueMiddleware($middleWareClass, $priority = 50)
    {
        if (in_array($middleWareClass, $this->keyMiddlewareList)) {
            // Don't throw error if the user defines the default middleware classes.
            return $this;
        }

        $this->checkPipelineIsStarted();
        $this->checkDuplicates($middleWareClass);
        $this->checkClassType($middleWareClass);

        if ($priority === 0 || $priority == 100) {
            $priority++;
        }

        if (!isset($this->priorityList[$priority])) {
            $this->priorityList[$priority] = [];
        }

        if (!in_array($middleWareClass, $this->priorityList[$priority])) {
            $this->priorityList[$priority][] = $middleWareClass;
        }

        return $this;
    }

    /**
     * Checks if the pipline is already being processed.
     *
     * @throws RuntimeException
     */
    private function checkPipelineIsStarted()
    {
        if (isset($this->index)) {
            throw new RuntimeException('You are forbidden to add new middleware after start.');
        }
    }

    /**
     * Checks if the given middleware is already added to the list.
     *
     * @param string $middleWareClass
     *
     * @throws RuntimeException
     */
    private function checkDuplicates($middleWareClass)
    {
        if (in_array($middleWareClass, $this->pipelineList)) {
            throw new InvalidArgumentException(
                sprintf('The class "%s" is already added to the pipeline.', $middleWareClass)
            );
        }
    }

    /**
     * Checks if the given class is a middleware.
     *
     * @param string $middleWareClass
     *
     * @throws RuntimeException
     */
    private function checkClassType($middleWareClass)
    {
        $interfaces = class_implements($middleWareClass);

        if ($interfaces && !in_array(MiddlewareInterface::class, $interfaces)) {
            throw new InvalidArgumentException(
                sprintf('The class "%s" does not implement MiddlewareInterface.', $middleWareClass)
            );
        }
    }

    /**
     * Sorts the pipeline elements according to the priority.
     */
    private function sortPipeline()
    {
        ksort($this->priorityList);
        $this->pipelineList = [];

        foreach ($this->priorityList as $middlewareList) {
            $this->pipelineList = array_merge($this->pipelineList, $middlewareList);
        }
    }

    /**
     * Starts the pipeline.
     *
     * @return null|string
     */
    public function start()
    {
        $this->index = 0;
        $this->sortPipeline();

        return $this->next();
    }

    /**
     * Gets next element from the pipeline.
     *
     * @return null|string
     */
    public function next()
    {
        if (!isset($this->index)) {
            throw new RuntimeException('Unable to get the next element until the pipeline is not started.');
        }

        return isset($this->pipelineList[$this->index]) ? $this->pipelineList[$this->index++] : null;
    }
}
