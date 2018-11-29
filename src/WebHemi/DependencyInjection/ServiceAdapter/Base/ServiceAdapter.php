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

namespace WebHemi\DependencyInjection\ServiceAdapter\Base;

use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;
use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface;
use WebHemi\DependencyInjection\ServiceAdapter\AbstractAdapter;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractAdapter
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }

    /**
     * Returns true if the given service is registered.
     *
     * @param  string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool
    {
        return isset($this->container[$identifier])
            || isset($this->serviceLibrary[$identifier])
            || class_exists($identifier);
    }

    /**
     * Gets a service.
     *
     * @param  string $identifier
     * @throws RuntimeException
     * @throws ReflectionException
     * @return object
     */
    public function get(string $identifier)
    {
        // Not registered but valid class name, so register it
        if (!isset($this->serviceLibrary[$identifier]) && class_exists($identifier)) {
            $this->registerService($identifier);
        }

        // The service is registered in the library but not in the container, so register it into the container too.
        if (!isset($this->container[$identifier])) {
            $this->registerServiceToContainer($identifier);
        }

        $service = $this->serviceLibrary[$identifier][self::SERVICE_SHARE]
            ? $this->container[$identifier]
            : clone $this->container[$identifier];

        return $service;
    }

    /**
     * Registers the service into the container AKA create the instance.
     *
     * @param  string $identifier
     * @throws ReflectionException
     * @return ServiceAdapter
     */
    private function registerServiceToContainer(string $identifier) : ServiceAdapter
    {
        // At this point the service must be in the library
        if (!isset($this->serviceLibrary[$identifier])) {
            throw new InvalidArgumentException(
                sprintf('Invalid service name: %s, service is not in the library.', $identifier),
                1000
            );
        }

        // Check arguments.
        $argumentList = $this
            ->setArgumentListReferences($this->serviceLibrary[$identifier][self::SERVICE_ARGUMENTS]);

        // Create new instance.
        $className = $this->serviceLibrary[$identifier][self::SERVICE_CLASS];
        $reflectionClass = new ReflectionClass($className);
        $serviceInstance = $reflectionClass->newInstanceArgs($argumentList);

        // Perform post init method calls.
        foreach ($this->serviceLibrary[$identifier][self::SERVICE_METHOD_CALL] as $methodCallList) {
            $method = $methodCallList[0];
            $argumentList = $this->setArgumentListReferences($methodCallList[1] ?? []);

            call_user_func_array([$serviceInstance, $method], $argumentList);
        }

        // Register sevice.
        $this->container[$identifier] = $serviceInstance;

        // Mark as initialized.
        $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED] = true;

        return $this;
    }

    /**
     * Tries to identify referce services in the argument list.
     *
     * @param  array $argumentList
     * @throws ReflectionException
     * @return array
     */
    private function setArgumentListReferences(array $argumentList) : array
    {
        foreach ($argumentList as $key => &$value) {
            // Associative array keys marks literal values
            if (!is_numeric($key)) {
                continue;
            }

            $value = $this->get($value);
        }

        return $argumentList;
    }

    /**
     * Register the service object instance.
     *
     * @param  string $identifier
     * @param  object $serviceInstance
     * @return ServiceInterface
     */
    public function registerServiceInstance(string $identifier, $serviceInstance) : ServiceInterface
    {
        // Check if the service is not initialized yet.
        if (!$this->serviceIsInitialized($identifier)) {
            $instanceType = gettype($serviceInstance);

            // Register synthetic services
            if ('object' !== $instanceType) {
                throw new InvalidArgumentException(
                    sprintf('The second parameter must be an object instance, %s given.', $instanceType),
                    1001
                );
            }

            // Register sevice.
            $this->container[$identifier] = $serviceInstance;

            // Overwrite any previous settings.
            $this->serviceLibrary[$identifier] = [
                self::SERVICE_INITIALIZED => true,
                self::SERVICE_ARGUMENTS => [],
                self::SERVICE_METHOD_CALL => [],
                self::SERVICE_SHARE => true,
                self::SERVICE_CLASS => get_class($serviceInstance),
            ];
        }

        return $this;
    }
}
