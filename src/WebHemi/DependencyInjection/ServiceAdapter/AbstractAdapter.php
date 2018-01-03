<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
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
    public const SERVICE_SOURCE_MODULE = 'source_module';
    public const SERVICE_CLASS = 'class';
    public const SERVICE_ARGUMENTS = 'arguments';
    public const SERVICE_METHOD_CALL = 'calls';
    public const SERVICE_SHARE = 'shared';
    public const SERVICE_SYNTHETIC = 'synthetic';
    public const SERVICE_INHERIT = 'inherits';
    public const SERVICE_INITIALIZED = 'initialized';

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
     * @param string $moduleName
     * @return ServiceInterface
     */
    public function registerService(string $identifier, string $moduleName = 'Global') : ServiceInterface
    {
        // Check if the service is not initialized yet.
        if (!$this->serviceIsInitialized($identifier)) {
            // overwrite if it was registered earlier.
            $this->serviceLibrary[$identifier] = [
                self::SERVICE_SOURCE_MODULE => $moduleName,
                self::SERVICE_INITIALIZED => false,
                self::SERVICE_ARGUMENTS => $this->resolveServiceArguments($identifier, $moduleName),
                self::SERVICE_METHOD_CALL => $this->resolveMethodCalls($identifier, $moduleName),
                self::SERVICE_SHARE => $this->resolveShares($identifier, $moduleName),
                self::SERVICE_CLASS => $this->resolveServiceClassName($identifier, $moduleName),
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
     * @param string $moduleName
     * @return array
     */
    public function getServiceConfiguration(string $identifier, string $moduleName = null) : array
    {
        $configuration = $this->serviceLibrary[$identifier] ?? [];

        if (isset($configuration[self::SERVICE_SOURCE_MODULE])
            && ($configuration[self::SERVICE_SOURCE_MODULE] == $moduleName || is_null($moduleName))
        ) {
            return $configuration;
        }

        // Get all registered module configurations and merge them together.
        $this->getAllRegisteredModuleConfigurations($configuration, $moduleName.'/'.$identifier);

        // Resolve inheritance.
        $this->resolveInheritance($configuration, $identifier);

        $this->serviceConfiguration[$identifier] = $configuration;

        return $configuration;
    }

    /**
     * Get all registered module configurations and merge them together.
     *
     * @param array $configuration
     * @param string $path
     */
    protected function getAllRegisteredModuleConfigurations(array &$configuration, string $path) : void
    {
        if ($this->configuration->has($path)) {
            $moduleConfig = $this->configuration->getData($path);
            $configuration = merge_array_overwrite($configuration, $moduleConfig);
        }
    }

    /**
     * Resolves the config inheritance.
     *
     * @param array $configuration
     * @param string $identifier
     */
    protected function resolveInheritance(array &$configuration, string $identifier) : void
    {
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
    }

    /**
     * Retrieves real service class name.
     *
     * @param string $identifier
     * @param string $moduleName
     * @return string
     */
    protected function resolveServiceClassName(string $identifier, string $moduleName) : string
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier, $moduleName);
        $className = $serviceConfiguration[self::SERVICE_CLASS] ?? $identifier;

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
     * @param string $moduleName
     * @return array
     */
    protected function resolveServiceArguments(string $identifier, string $moduleName) : array
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier, $moduleName);

        return $serviceConfiguration[self::SERVICE_ARGUMENTS] ?? [];
    }

    /**
     * Returns the service post-init method calls.
     *
     * @param string $identifier
     * @param string $moduleName
     * @return array
     */
    protected function resolveMethodCalls(string $identifier, string $moduleName) : array
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier, $moduleName);

        return $serviceConfiguration[self::SERVICE_METHOD_CALL] ?? [];
    }

    /**
     * Returns the service share status.
     *
     * @param string $identifier
     * @param string $moduleName
     * @return bool
     */
    protected function resolveShares(string $identifier, string $moduleName) : bool
    {
        $serviceConfiguration = $this->getServiceConfiguration($identifier, $moduleName);

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
            $this->registerService(current($services), $moduleName);
            next($services);
        }
        return $this;
    }
}
