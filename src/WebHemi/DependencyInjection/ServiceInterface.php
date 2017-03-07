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
     * Register the service.
     *
     * @param string        $identifier
     * @param string|object $serviceClass
     * @return ServiceInterface
     */
    public function registerService(string $identifier, $serviceClass) : ServiceInterface;

    /**
     * Gets a service.
     *
     * @param string $identifier
     * @return object
     */
    public function get(string $identifier);

    /**
     * Returns true if the given service is defined.
     *
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool;

    /**
     * Register module specific services.
     * If a service is already registered in the Global namespace, it will be skipped.
     *
     * @param string $moduleName
     * @return ServiceInterface
     */
    public function registerModuleServices(string $moduleName) : ServiceInterface;

    /**
     * Sets service argument.
     *
     * @param mixed $service
     * @param mixed $parameter
     * @return ServiceInterface
     */
    public function setServiceArgument($service, $parameter) : ServiceInterface;
}
