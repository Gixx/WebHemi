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

namespace WebHemi\Adapter\DependencyInjection;

use WebHemi\Config\ConfigInterface;

/**
 * Interface DependencyInjectionAdapterInterface.
 */
interface DependencyInjectionAdapterInterface
{
    /**
     * DependencyInjectionAdapterInterface constructor.
     *
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration);

    /**
     * Register the service.
     *
     * @param string        $identifier
     * @param string|object $serviceClass
     * @return DependencyInjectionAdapterInterface
     */
    public function registerService(string $identifier, $serviceClass) : DependencyInjectionAdapterInterface;

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
     * @return DependencyInjectionAdapterInterface
     */
    public function registerModuleServices(string $moduleName) : DependencyInjectionAdapterInterface;

    /**
     * Sets service argument.
     *
     * @param mixed $service
     * @param mixed $parameter
     * @return DependencyInjectionAdapterInterface
     */
    public function setServiceArgument($service, $parameter) : DependencyInjectionAdapterInterface;
}
