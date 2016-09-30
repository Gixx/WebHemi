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
namespace WebHemi\Application;

use RuntimeException;
use WebHemi\Config\ConfigInterface;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\RoutingMiddleware;

/**
 * Class PipelineManager.
 */
class PipelineManager
{
    /** @var ConfigInterface */
    private $config;
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
     *
     * @param ConfigInterface $pipelineConfig
     */
    public function __construct(ConfigInterface $pipelineConfig)
    {
        $this->config = $pipelineConfig;
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

        $this->buildPipeline();
    }

    /**
     * Add middleware definitions to the pipeline.
     */
    private function buildPipeline()
    {
        $pipelineConfig = $this->config->toArray();

        foreach ($pipelineConfig as $middlewareData) {
            if (!isset($middlewareData['priority'])) {
                $middlewareData['priority'] = 50;
            }

            $this->queueMiddleware($middlewareData['service'], $middlewareData['priority']);
        }
    }

    /**
     * Checks the given class against Middleware Criterias.
     *
     * @param $middleWareClass
     *
     * @throws RuntimeException
     *
     * @return bool
     */
    private function checkMiddleware($middleWareClass)
    {
        if (isset($this->index)) {
            throw new RuntimeException('You are forbidden to add new middleware after start.', 1000);
        }

        if (in_array($middleWareClass, $this->pipelineList)) {
            throw new RuntimeException(
                sprintf('The service "%s" is already added to the pipeline.', $middleWareClass),
                1001
            );
        }

        if (class_exists($middleWareClass)
            && !array_key_exists(MiddlewareInterface::class, class_implements($middleWareClass))
        ) {
            throw new RuntimeException(
                sprintf('The service "%s" is not a middleware.', $middleWareClass),
                1002
            );
        }

        return true;
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
        $this->checkMiddleware($middleWareClass);

        if (in_array($middleWareClass, $this->keyMiddlewareList)) {
            // Don't throw error if the user defines the default middleware classes.
            return $this;
        }

        if ($priority === 0 || $priority == 100) {
            $priority++;
        }

        if (!isset($this->priorityList[$priority])) {
            $this->priorityList[$priority] = [];
        }

        if (!in_array($middleWareClass, $this->pipelineList)) {
            $this->priorityList[$priority][] = $middleWareClass;
            $this->pipelineList[] = $middleWareClass;
        }

        return $this;
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
            throw new RuntimeException('Unable to get the next element until the pipeline is not started.', 1003);
        }

        return isset($this->pipelineList[$this->index]) ? $this->pipelineList[$this->index++] : null;
    }

    /**
     * Gets the full pipeline list.
     *
     * @return array
     */
    public function getPipelineList()
    {
        return $this->pipelineList;
    }
}
