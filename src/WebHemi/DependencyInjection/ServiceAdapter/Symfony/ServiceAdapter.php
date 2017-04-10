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

namespace WebHemi\DependencyInjection\ServiceAdapter\Symfony;

use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    private const SERVICE_CLASS = 'class';
    private const SERVICE_ARGUMENTS = 'arguments';
    private const SERVICE_METHOD_CALL = 'calls';
    private const SERVICE_SHARE = 'shared';
    private const SERVICE_INHERIT = 'inherits';

    /** @var ContainerBuilder */
    private $container;
    /** @var array */
    private $configuration;
    /** @var string */
    private $moduleNamespace;
    /** @var array */
    private $servicesToDefine = [];
    /** @var array */
    private $instantiatedSharedServices = [];
    /** @var array */
    private $defaultSetUpData = [
        self::SERVICE_CLASS         => '',
        self::SERVICE_ARGUMENTS     => [],
        self::SERVICE_METHOD_CALL   => [],
        // By default the Symfony DI shares all services. In WebHemi by default nothing is shared.
        self::SERVICE_SHARE         => false,
    ];
    /** @var int */
    private static $parameterIndex = 0;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->container = new ContainerBuilder();
        $this->configuration = $configuration->getData('dependencies');
    }

    /**
     * Initializes the DI container from the config.
     *
     * @param array  $dependencies
     * @return ServiceAdapter
     */
    private function registerServices(array $dependencies) : ServiceAdapter
    {
        // Collect the name information about the services to be registered
        foreach ($dependencies as $alias => $setupData) {
            $this->servicesToDefine[$alias] = $this->getRealServiceClass($setupData, $alias);
        }

        foreach ($this->servicesToDefine as $alias => $serviceClass) {
            $this->registerService($alias, $serviceClass);
        }

        return $this;
    }

    /**
     * Gets real service class name.
     *
     * @param array  $setupData
     * @param string $alias
     * @return string
     */
    private function getRealServiceClass(array $setupData, string $alias) : string
    {
        if (isset($setupData[self::SERVICE_CLASS])) {
            $serviceClass = $setupData[self::SERVICE_CLASS];
        } else {
            $serviceClass = $alias;
        }

        return $serviceClass;
    }

    /**
     * Registers the service.
     *
     * @param string        $identifier
     * @param string|object $serviceClass
     * @return ServiceInterface
     */
    public function registerService(string $identifier, $serviceClass) : ServiceInterface
    {
        // Do nothing if the service has been already registered with the same alias.
        // It is allowed to register the same service multiple times with different aliases.
        if ($this->has($identifier)) {
            return $this;
        }

        // Register synthetic services
        if ('object' == gettype($serviceClass)) {
            $this->container->register($identifier)
                ->setShared(true)
                ->setSynthetic(true);

            $this->container->set($identifier, $serviceClass);
            return $this;
        }

        $setUpData = $this->getServiceSetupData($identifier, $serviceClass);

        // Create the definition.
        $definition = new Definition($serviceClass);

        $sharedService = (bool) $setUpData[self::SERVICE_SHARE];
        $definition->setShared($sharedService);

        // Register the service.
        $service = $this->container->setDefinition($identifier, $definition);

        if ($sharedService) {
            $this->instantiatedSharedServices[$service->getClass()] = false;
        }

        // Add arguments.
        foreach ((array) $setUpData[self::SERVICE_ARGUMENTS] as $parameter) {
            $this->setServiceArgument($service, $parameter);
        }

        // Register method callings.
        foreach ((array) $setUpData[self::SERVICE_METHOD_CALL] as $method => $parameterList) {
            $this->addMethodCall($service, $method, $parameterList);
        }

        return $this;
    }

    /**
     * Gets the set up data for the service registration.
     *
     * @param string $identifier
     * @param string $serviceClass
     * @return array
     */
    private function getServiceSetupData(string $identifier, string $serviceClass) : array
    {
        // Init settings.
        $setUpData = $this->defaultSetUpData;
        $setUpData[self::SERVICE_CLASS] = $serviceClass;

        // Override settings from the Global configuration if exists.
        if (isset($this->configuration['Global'][$identifier])) {
            $setUpData = array_merge($setUpData, $this->configuration['Global'][$identifier]);
        }

        // Override settings from the Module configuration if exists.
        if (!empty($this->moduleNamespace) && isset($this->configuration[$this->moduleNamespace][$identifier])) {
            $setUpData = array_merge($setUpData, $this->configuration[$this->moduleNamespace][$identifier]);
        }

        if (isset($setUpData[self::SERVICE_INHERIT])) {
            $setUpData = $this->resolveInheritance($setUpData);
        }

        return $setUpData;
    }

    /**
     * Resolves inheritance. Could solve in within the getServiceSetupData method but it would dramatically increase the
     * code complexity index.
     *
     * @param array $setUpData
     * @return array
     */
    private function resolveInheritance(array $setUpData) : array
    {
        // Clear all empty init parameters.
        foreach ($setUpData as $key => $data) {
            if ($data === [] || $data === '') {
                unset($setUpData[$key]);
            }
        }
        // Recursively get the inherited configuration.
        $inheritSetUpData = $this->getServiceSetupData(
            $setUpData[self::SERVICE_INHERIT],
            $setUpData[self::SERVICE_INHERIT]
        );

        $setUpData = array_merge($inheritSetUpData, $setUpData);
        unset($setUpData[self::SERVICE_INHERIT]);

        return $setUpData;
    }

    /**
     * Adds a method call for the service. It will be triggered as soon as the service had been initialized.
     *
     * @param Definition $service
     * @param string     $method
     * @param array      $parameterList
     * @return void
     */
    private function addMethodCall(Definition $service, string $method, array $parameterList = []) : void
    {
        // Check the parameter list for reference services
        foreach ($parameterList as &$parameter) {
            $parameter = $this->getReferenceServiceIfAvailable($parameter);
        }

        $service->addMethodCall($method, $parameterList);
    }

    /**
     * If possible create register the parameter as a service and give it back as a reference.
     *
     * @param mixed $classOrServiceName
     * @return mixed|Reference
     */
    private function getReferenceServiceIfAvailable($classOrServiceName)
    {
        $reference = $classOrServiceName;

        // Check string parameter if it is a valid service or class name.
        if (!is_string($classOrServiceName)) {
            return $reference;
        }

        if (isset($this->servicesToDefine[$classOrServiceName])) {
            // The parameter is defined as a service but it is not yet registered; alias is given.
            $this->registerService($classOrServiceName, $this->servicesToDefine[$classOrServiceName]);
        } elseif (in_array($classOrServiceName, $this->servicesToDefine)) {
            // The parameter is defined as a service but it is not yet registered; real class is given.
            $referenceAlias = array_search($classOrServiceName, $this->servicesToDefine);
            $this->registerService($referenceAlias, $this->servicesToDefine[$referenceAlias]);
            $classOrServiceName = $referenceAlias;
        } elseif (class_exists($classOrServiceName)) {
            // The parameter is not a service, but it is a class that can be instantiated. e.g.: DateTime::class
            $this->container->register($classOrServiceName, $classOrServiceName);
        }

        if ($this->has($classOrServiceName)) {
            $reference = new Reference($classOrServiceName);
        }

        return $reference;
    }

    /**
     * Creates a safe normalized name.
     *
     * @param string $className
     * @param string $argumentName
     * @return string
     */
    private function getNormalizedName(string $className, string $argumentName) : string
    {
        $className = 'C_'.preg_replace('/[^a-z0-9]/', '', strtolower($className));
        $argumentName = 'A_'.preg_replace('/[^a-z0-9]/', '', strtolower($argumentName));

        return $className.'.'.$argumentName;
    }

    /**
     * Gets a service. It also tries to register the one without arguments which not yet registered.
     *
     * @param string $identifier
     * @return object
     */
    public function get(string $identifier)
    {
        if (!$this->container->has($identifier) && class_exists($identifier)) {
            $this->registerService($identifier, $identifier);
        }

        $service = $this->container->get($identifier);
        $serviceClass = get_class($service);

        if (isset($this->instantiatedSharedServices[$serviceClass])) {
            $this->instantiatedSharedServices[$serviceClass] = true;
        }

        return $service;
    }

    /**
     * Returns true if the given service is defined.
     *
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool
    {
        return $this->container->has($identifier);
    }

    /**
     * Register module specific services.
     * If a service is already registered, it will be skipped.
     *
     * @param string $moduleName
     * @return ServiceInterface
     */
    public function registerModuleServices(string $moduleName) : ServiceInterface
    {
        if (isset($this->configuration[$moduleName])) {
            $this->moduleNamespace = $moduleName;
            $this->registerServices($this->configuration[$moduleName]);
        }

        return $this;
    }

    /**
     * Sets service argument.
     *
     * @param string|Definition $service
     * @param mixed             $parameter
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function setServiceArgument($service, $parameter) : ServiceInterface
    {
        $service = $this->getRealService($service);
        $parameterName = $this->getRealParameterName($parameter);
        $serviceClass = $service->getClass();

        // Check if service is shared and is already initialized.
        $this->checkSharedServiceClassState($serviceClass);

        // Create a normalized name for the argument.
        $normalizedName = $this->getNormalizedName($serviceClass, $parameterName);

        // If the parameter marked as to be used as a scalar.
        if (is_scalar($parameter) && strpos((string) $parameter, '!:') === 0) {
            $parameter = substr((string) $parameter, 2);
        } else {
            // Otherwise check if the parameter is a service.
            $parameter = $this->getReferenceServiceIfAvailable($parameter);
        }

        $this->container->setParameter($normalizedName, $parameter);
        $service->addArgument('%'.$normalizedName.'%');

        return $this;
    }

    /**
     * Gets the real service instance.
     *
     * @param mixed $service
     * @return Definition
     */
    private function getRealService($service) : Definition
    {
        if (!$service instanceof Definition) {
            $service = $this->container->getDefinition($service);
        }

        return $service;
    }

    /**
     * Gets the real parameter name.
     *
     * @param mixed $parameterName
     * @return string
     */
    private function getRealParameterName($parameterName) : string
    {
        if (!is_scalar($parameterName)) {
            $parameterName = self::$parameterIndex++;
        }

        return (string) $parameterName;
    }

    /**
     * Checks whether the service is shared and initialized
     *
     * @param string $serviceClass
     * @throws RuntimeException
     * @return void
     */
    private function checkSharedServiceClassState(string $serviceClass) : void
    {
        if (isset($this->instantiatedSharedServices[$serviceClass])
            && $this->instantiatedSharedServices[$serviceClass] === true
        ) {
            throw new RuntimeException('Cannot add argument to an already initialized service.', 1000);
        }
    }
}
