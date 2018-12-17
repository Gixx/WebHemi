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

namespace WebHemi\MiddlewarePipeline\ServiceAdapter;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\MiddlewarePipeline\ServiceInterface;

/**
 * Class AbstractAdapter.
 */
abstract class AbstractAdapter implements ServiceInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var array
     */
    protected $priorityList;
    /**
     * @var array
     */
    protected $pipelineList;
    /**
     * @var array
     */
    protected $keyMiddlewareList;
    /**
     * @var int
     */
    protected $index;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration->getConfig('middleware_pipeline');
        $this->initProperties();
        $this->buildPipeline();
    }

    /**
     * Initialize the listing properties (priorityList, pipelineList, keyMiddlewareList).
     *
     * @return void
     */
    abstract protected function initProperties() : void;

    /**
     * Add middleware definitions to the pipeline.
     *
     * @param  string $moduleName
     * @return void
     */
    protected function buildPipeline(string $moduleName = 'Global') : void
    {
        $pipelineConfig = $this->configuration->getData($moduleName);

        foreach ($pipelineConfig as $middlewareData) {
            if (!isset($middlewareData['priority'])) {
                $middlewareData['priority'] = 50;
            }

            $this->queueMiddleware($middlewareData['service'], $middlewareData['priority']);
        }
    }

    /**
     * Checks the given class against Middleware Criteria.
     *
     * @param  string $middleWareClass
     * @throws RuntimeException
     * @return bool
     */
    protected function checkMiddleware(string $middleWareClass) : bool
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
     * Adds module specific pipeline.
     *
     * @param  string $moduleName
     * @return ServiceInterface
     */
    public function addModulePipeLine(string $moduleName) : ServiceInterface
    {
        $this->buildPipeline($moduleName);

        return $this;
    }

    /**
     * Adds a new middleware to the pipeline queue.
     *
     * @param  string $middleWareClass
     * @param  int    $priority
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function queueMiddleware(string $middleWareClass, int $priority = 50) : ServiceInterface
    {
        $this->checkMiddleware($middleWareClass);

        if (in_array($middleWareClass, $this->keyMiddlewareList, true)) {
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
     *
     * @return void
     */
    protected function sortPipeline() : void
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
    public function start() : ? string
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
    public function next() : ? string
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
    public function getPipelineList() : array
    {
        return $this->pipelineList;
    }
}
