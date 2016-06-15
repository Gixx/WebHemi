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

use WebHemi\Config\ConfigInterface;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;

/**
 * Interface ApplicationInterface.
 */
interface ApplicationInterface
{
    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface  $container
     * @param ConfigInterface $config
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
     * Sets application environments according to the super globals. This is typically good to choose between
     * application modules, like 'Website' or 'Admin'.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $cookie
     * @param array $files
     *
     * @return ApplicationInterface
     */
    public function setEnvironmentFromGlobals(array $get, array $post, array $server, array $cookie, array $files);

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    public function run();
}
