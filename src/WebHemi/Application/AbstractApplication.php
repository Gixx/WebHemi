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

use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;

/**
 * Class AbstractApplication.
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /** @var DependencyInjectionAdapterInterface */
    private $container;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var PipelineManager */
    private $pipelineManager;

    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     * @param EnvironmentManager                  $environmentManager
     * @param PipelineManager                     $pipelineManager
     */
    public function __construct(
        DependencyInjectionAdapterInterface $container,
        EnvironmentManager $environmentManager,
        PipelineManager $pipelineManager
    ) {
        $this->container = $container;
        $this->environmentManager = $environmentManager;
        $this->pipelineManager = $pipelineManager;
    }

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    final public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets the environment manager instance.
     *
     * @return EnvironmentManager
     */
    final public function getEnvironmentManager()
    {
        return $this->environmentManager;
    }

    /**
     * Gets the pipeline manager instance.
     *
     * @return PipelineManager
     */
    final public function getPipelineManager()
    {
        return $this->pipelineManager;
    }

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    abstract public function run();
}
