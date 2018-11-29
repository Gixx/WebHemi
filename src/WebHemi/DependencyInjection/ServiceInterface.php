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

namespace WebHemi\DependencyInjection;

use WebHemi\Configuration\ServiceInterface as ConfigurationService;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationService $configuration
     */
    public function __construct(ConfigurationService $configuration);

    /**
     * Returns true if the given service is registered.
     *
     * @param  string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool;

    /**
     * Gets a service.
     *
     * @param  string $identifier
     * @return object
     */
    public function get(string $identifier);

    /**
     * Retrieves configuration for a service.
     *
     * @param  string $identifier
     * @param  string $moduleName
     * @return array
     */
    public function getServiceConfiguration(string $identifier, string $moduleName = null) : array;

    /**
     * Register the service.
     *
     * @param  string $identifier
     * @param  string $moduleName
     * @return ServiceInterface
     */
    public function registerService(string $identifier, string $moduleName = 'Global') : ServiceInterface;

    /**
     * Register the service object instance.
     *
     * @param  string $identifier
     * @param  object $serviceInstance
     * @return ServiceInterface
     */
    public function registerServiceInstance(string $identifier, $serviceInstance) : ServiceInterface;


    /**
     * Register module specific services.
     * If a service is already registered in the Global namespace, it will be skipped.
     *
     * @param  string $moduleName
     * @return ServiceInterface
     */
    public function registerModuleServices(string $moduleName) : ServiceInterface;
}
