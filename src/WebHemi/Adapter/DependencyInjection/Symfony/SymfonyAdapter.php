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
namespace WebHemi\Adapter\DependencyInjection\Symfony;

use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Config\ConfigInterface;

/**
 * Class SymfonyAdapter.
 */
class SymfonyAdapter implements DependencyInjectionAdapterInterface
{
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
    /** @var int */
    private static $parameterIndex = 0;

    /**
     * DependencyInjectionAdapterInterface constructor.
     *
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration)
    {
        $this->container = new ContainerBuilder();
        $this->configuration = (array) $configuration->getData('dependencies');
    }

    /**
     * Initializes the DI container from the config.
     *
     * @param array  $dependencies
     * @return SymfonyAdapter
     */
    private function registerServices(array $dependencies)
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
     * @return mixed
     */
    private function getRealServiceClass(array $setupData, $alias)
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
     * @return SymfonyAdapter
     */
    public function registerService($identifier, $serviceClass)
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
    private function getServiceSetupData($identifier, $serviceClass)
    {
        // Init settings.
        $setUpData = [
            self::SERVICE_CLASS       => $serviceClass,
            self::SERVICE_ARGUMENTS   => [],
            self::SERVICE_METHOD_CALL => [],
            // By default the Symfony DI shares all services. In WebHemi by default nothing is shared.
            self::SERVICE_SHARE       => false,
        ];

        // Override settings from the configuration if exists.
        if (isset($this->configuration['Global'][$identifier])) {
            $setUpData = array_merge($setUpData, $this->configuration['Global'][$identifier]);
        } elseif (!empty($this->moduleNamespace) && isset($this->configuration[$this->moduleNamespace][$identifier])) {
            $setUpData = array_merge($setUpData, $this->configuration[$this->moduleNamespace][$identifier]);
        }

        return $setUpData;
    }

    /**
     * Adds a method call for the service. It will be triggered as soon as the service had been initialized.
     *
     * @param Definition $service
     * @param string     $method
     * @param array      $parameterList
     */
    private function addMethodCall(Definition $service, $method, $parameterList = [])
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
     *
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
     *
     * @return string
     */
    private function getNormalizedName($className, $argumentName)
    {
        $className = 'C_'.preg_replace('/[^a-z0-9]/', '', strtolower($className));
        $argumentName = 'A_'.preg_replace('/[^a-z0-9]/', '', strtolower($argumentName));

        return $className.'.'.$argumentName;
    }

    /**
     * Gets a service. It also tries to register the one without arguments which not yet registered.
     *
     * @param string $identifier
     *
     * @return object
     */
    public function get($identifier)
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
     *
     * @return bool
     */
    public function has($identifier)
    {
        return $this->container->has($identifier);
    }

    /**
     * Register module specific services.
     * If a service is already registered, it will be skipped.
     *
     * @param string $moduleName
     * @return DependencyInjectionAdapterInterface
     */
    public function registerModuleServices($moduleName)
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
     * @param Definition $service
     * @param mixed      $parameter
     *
     * @throws RuntimeException
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function setServiceArgument($service, $parameter)
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
    private function getRealService($service)
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
    private function getRealParameterName($parameterName)
    {
        if (!is_scalar($parameterName)) {
            $parameterName = self::$parameterIndex++;
        }

        return $parameterName;
    }

    /**
     * Checks whether the service is shared and initialized
     *
     * @param string $serviceClass
     * @throws RuntimeException
     */
    private function checkSharedServiceClassState($serviceClass)
    {
        if (isset($this->instantiatedSharedServices[$serviceClass])
            && $this->instantiatedSharedServices[$serviceClass] === true
        ) {
            throw new RuntimeException('Cannot add argument to an already initialized service.');
        }
    }
}
