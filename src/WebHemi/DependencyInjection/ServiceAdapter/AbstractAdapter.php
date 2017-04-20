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

namespace WebHemi\DependencyInjection\ServiceAdapter;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface;

/**
 * Class AbstractAdapter
 */
abstract class AbstractAdapter implements ServiceInterface
{
    const SERVICE_CLASS = 'class';
    const SERVICE_ARGUMENTS = 'arguments';
    const SERVICE_METHOD_CALL = 'calls';
    const SERVICE_SHARE = 'shared';
    const SERVICE_SYNTHETIC = 'synthetic';
    const SERVICE_INHERIT = 'inherits';
    const SERVICE_INITIALIZED = 'initialized';

    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var array */
    protected $registeredModules = [];
    /** @var array */
    protected $serviceLibrary = [];
    /** @var array */
    protected $serviceConfiguration = [];

    /**
     * AbstractAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration->getConfig('dependencies');
    }

    /**
     * Returns true if the given service is registered.
     *
     * @param string $identifier
     * @return bool
     */
    abstract public function has(string $identifier) : bool;

    /**
     * Gets a service.
     *
     * @param string $identifier
     * @return object
     */
    abstract public function get(string $identifier);

    /**
     * Register the service.
     *
     * @param string $identifier
     * @return ServiceInterface
     */
    public function registerService(string $identifier) : ServiceInterface
    {
        // Check if the service is not initialized yet.
        if (!$this->serviceIsInitialized($identifier)) {
            // overwrite if it was registered earlier.
            $this->serviceLibrary[$identifier] = [
                self::SERVICE_INITIALIZED => false,
                self::SERVICE_ARGUMENTS => $this->resolveServiceArguments($identifier),
                self::SERVICE_METHOD_CALL => $this->resolveMethodCalls($identifier),
                self::SERVICE_SHARE => $this->resolveShares($identifier),
                self::SERVICE_CLASS => $this->resolveServiceClassName($identifier),
            ];
        }

        return $this;
    }

    /**
     * Checks if the service has been already initialized.
     *
     * @param string $identifier
     * @return bool
     */
    protected function serviceIsInitialized(string $identifier) : bool
    {
        return isset($this->serviceLibrary[$identifier])
            && $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED];
    }

    /**
     * Retrieves configuration for a service.
     *
     * @param string $identifier
     * @return array
     */
    private function getServiceConfiguration(string $identifier) : array
    {
        if (isset($this->serviceLibrary[$identifier])) {
            return $this->serviceLibrary[$identifier];
        }

        $configuration = [];

        // Get all registered module configurations and merge them together.
        foreach ($this->registeredModules as $moduleName) {
            if ($this->configuration->has($moduleName.'/'.$identifier)) {
                $moduleConfig = $this->configuration->getData($moduleName.'/'.$identifier);
                $configuration = merge_array_overwrite($configuration, $moduleConfig);
            }
        }

        // Resolve inheritance.
        if (isset($configuration[self::SERVICE_INHERIT])) {
            $parentConfiguration = $this->getServiceConfiguration($configuration[self::SERVICE_INHERIT]);

            foreach ($configuration as $key => $value) {
                $parentConfiguration[$key] = $value;
            }

            // If the class name is not explicitly defined but the identifier is a class, the inherited class name
            // should be overwritten.
            if (!isset($configuration[self::SERVICE_CLASS]) && class_exists($identifier)) {
                $parentConfiguration[self::SERVICE_CLASS] = $identifier;
            }

            $configuration = $parentConfiguration;
            unset($parentConfiguration, $configuration[self::SERVICE_INHERIT]);
        }

        $this->serviceConfiguration[$identifier] = $configuration;

        return $configuration;
    }

    /**
     * Retrieves real service class name.
     *
     * @param string $identifier
     * @return string
     */
    protected function resolveServiceClassName(string $identifier) : string
    {
        if (isset($this->serviceLibrary[$identifier])) {
            // Class is already registered in the library so it must have a resolved class name.
            $className = $this->serviceLibrary[$identifier][self::SERVICE_CLASS];
        } else {
            $serviceConfiguration = $this->getServiceConfiguration($identifier);
            $className = $serviceConfiguration[self::SERVICE_CLASS] ?? $identifier;
        }

        if (!class_exists($className)) {
            throw new RuntimeException(
                sprintf('The resolved class "%s" cannot be found.', $className),
                1002
            );
        }

        return $className;
    }

    /**
     * Gets argument list and resolves alias references.
     *
     * @param string $identifier
     * @return array
     */
    protected function resolveServiceArguments(string $identifier) : array
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier);

        return $serviceConfiguration[self::SERVICE_ARGUMENTS] ?? [];
    }

    /**
     * Returns the service post-init method calls.
     *
     * @param string $identifier
     * @return array
     */
    protected function resolveMethodCalls(string $identifier) : array
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier);

        return $serviceConfiguration[self::SERVICE_METHOD_CALL] ?? [];
    }

    /**
     * Returns the service share status.
     *
     * @param string $identifier
     * @return bool
     */
    protected function resolveShares(string $identifier) : bool
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier);

        return $serviceConfiguration[self::SERVICE_SHARE] ?? false;
    }

    /**
     * Register the service.
     *
     * @param string  $identifier
     * @param object  $serviceInstance
     * @return ServiceInterface
     */
    abstract public function registerServiceInstance(string $identifier, $serviceInstance) : ServiceInterface;

    /**
     * Register module specific services.
     * If a service is already registered in the Global namespace, it will be skipped.
     *
     * @param string $moduleName
     * @return ServiceInterface
     */
    public function registerModuleServices(string $moduleName) : ServiceInterface
    {
        if (!$this->configuration->has($moduleName)) {
            throw new InvalidArgumentException(
                sprintf('\'%s\' is not a valid module name', $moduleName),
                1002
            );
        }

        $this->registeredModules[] = $moduleName;
        $services = array_keys($this->configuration->getData($moduleName));

        while (key($services) !== null) {
            $this->registerService(current($services));
            next($services);
        }
        return $this;
    }
}
