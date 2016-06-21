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
use WebHemi\Config\ConfigInterface;
use InvalidArgumentException;

/**
 * Interface ApplicationInterface.
 */
interface ApplicationInterface
{
    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     * @param ConfigInterface                     $config
     */
    public function __construct(DependencyInjectionAdapterInterface $container, ConfigInterface $config);

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function getContainer();

    /**
     * Returns the Configuration.
     *
     * @return ConfigInterface
     */
    public function getConfig();

    /**
     * Sets application environments according to the super globals.
     *
     * @param string $key
     * @param array  $data
     *
     * @throws InvalidArgumentException
     *
     * @return ApplicationInterface
     */
    public function setEnvironmentData($key, array $data);

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    public function run();
}
