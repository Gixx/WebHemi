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
namespace WebHemiTest\TestService;

use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use Throwable;
use WebHemi\DependencyInjection\ServiceInterface;
use WebHemi\DependencyInjection\ServiceAdapter\AbstractAdapter;

/**
 * Class EmptyDependencyInjectionContainer
 */
class EmptyDependencyInjectionContainer extends AbstractAdapter
{
    private $container = [];

    /**
     * Returns true if the given service is registered.
     *
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool
    {
        return isset($this->container[$identifier])
            || isset($this->serviceLibrary[$identifier])
            || class_exists($identifier);
    }

    /**
     * Makes the resolveServiceClassName() method to be public.
     *
     * @param string $identifier
     * @return string
     */
    public function callResolveServiceClassName(string $identifier) : string
    {
        return $this->resolveServiceClassName($identifier);
    }

    /**
     * Makes the resolveServiceArguments() method to be public.
     *
     * @param string $identifier
     * @return array
     */
    public function callResolveServiceArguments(string $identifier) : array
    {
        return $this->resolveServiceArguments($identifier);
    }

    /**
     * Makes the resolveShares() method to be public.
     *
     * @param string $identifier
     * @return bool
     */
    public function callResolveShares(string $identifier) : bool
    {
        return $this->resolveShares($identifier);
    }

    /**
     * Makes the moreAlias serviceIsInitialized() method public.
     *
     * @param string $identifier
     * @return bool
     */
    public function callServiceIsInitialized(string $identifier) : bool
    {
        return $this->serviceIsInitialized($identifier);
    }

    /**
     * Gets a service.
     *
     * @param string $identifier
     * @return object
     */
    public function get(string $identifier)
    {
        if (!$this->has($identifier)) {
            throw new InvalidArgumentException('Wrong service name: '.$identifier);
        }

        if (class_exists($identifier) && !isset($this->serviceLibrary[$identifier])) {
            $this->registerService($identifier);
        }

        $initialized = $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED] ?? false;

        if (!$initialized) {
            $this->registerLibraryInstance(
                $identifier,
                $this->serviceLibrary[$identifier][self::SERVICE_CLASS],
                $this->serviceLibrary[$identifier][self::SERVICE_ARGUMENTS]
            );
        }

        if (!isset($this->container[$identifier])) {
            throw new RuntimeException(
                sprintf('The service "%s" cannot be found.', $identifier),
                1002
            );
        }

        return $this->container[$identifier];
    }

    /**
     * @param string $identifier
     * @param string $className
     * @param array $arguments
     */
    private function registerLibraryInstance(string $identifier, string $className, array $arguments = [])
    {
        try {
            if (count($arguments) == 0) {
                $serviceInstance = new $className;
            } else {
                $reflectionClass = new ReflectionClass($className);
                $serviceInstance = $reflectionClass->newInstanceArgs($arguments);
            }
        } catch (Throwable $error) {
            $serviceInstance = null;
        }

        if ($serviceInstance) {
            $this->container[$identifier] = $serviceInstance;
            $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED] = true;
        }
    }

    /**
     * Register the service.
     *
     * @param string  $identifier
     * @param object  $serviceInstance
     * @return ServiceInterface
     */
    public function registerServiceInstance(string $identifier, $serviceInstance) : ServiceInterface
    {
        $instanceType = gettype($serviceInstance);

        // Register synthetic services
        if ('object' !== $instanceType) {
            throw new InvalidArgumentException(
                sprintf('The second parameter must be an object instance, %s given.', $instanceType),
                1001
            );
        }

        $this->container[$identifier] = $serviceInstance;
        $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED] = true;

        return $this;
    }
}
