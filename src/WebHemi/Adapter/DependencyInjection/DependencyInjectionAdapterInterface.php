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
namespace WebHemi\Adapter\DependencyInjection;

use WebHemi\Config\ConfigInterface;

/**
 * Interface DependencyInjectionAdapterInterface.
 */
interface DependencyInjectionAdapterInterface
{
    const SERVICE_CLASS = 'class';
    const SERVICE_ARGUMENTS = 'arguments';
    const SERVICE_METHOD_CALL = 'calls';
    const SERVICE_SHARE = 'shared';

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
    public function registerService($identifier, $serviceClass);

    /**
     * Gets a service.
     *
     * @param string $identifier
     *
     * @return object
     */
    public function get($identifier);

    /**
     * Returns true if the given service is defined.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * Register module specific services.
     * If a service is already registered in the Global namespace, it will be skipped.
     *
     * @param string $moduleName
     * @return DependencyInjectionAdapterInterface
     */
    public function registerModuleServices($moduleName);

    /**
     * Sets service argument.
     *
     * @param string $identifier
     * @param mixed  $parameter
     * @return DependencyInjectionAdapterInterface
     */
    public function setServiceArgument($identifier, $parameter);
}
