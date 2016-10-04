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
 * Interface ApplicationInterface.
 */
interface ApplicationInterface
{
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
    );

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function getContainer();

    /**
     * Gets the environment manager instance.
     *
     * @return EnvironmentManager
     */
    public function getEnvironmentManager();

    /**
     * Gets the pipeline manager instance.
     *
     * @return PipelineManager
     */
    public function getPipelineManager();

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    public function run();
}
