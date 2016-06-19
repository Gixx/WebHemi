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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Adapter\Exception\InitException;
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
    /** @var array */
    private $servicesToDefine = [];
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
        $this->configuration = $configuration->toArray();

        $this->initContainer();
    }

    /**
     * Initializes the DI container from the config.
     */
    private function initContainer()
    {
        // Collect the name information about the services to be registered
        foreach ($this->configuration as $alias => $setupData) {
            if (isset($setupData[self::SERVICE_CLASS])) {
                $serviceClass = $setupData[self::SERVICE_CLASS];
            } else {
                $serviceClass = $alias;
            }

            $this->servicesToDefine[$alias] = $serviceClass;
        }

        foreach ($this->servicesToDefine as $alias => $serviceClass) {
            $this->registerService($alias, $serviceClass);
        }
    }

    /**
     * Register the service.
     *
     * @param string $identifier
     * @param string $serviceClass
     */
    public function registerService($identifier, $serviceClass)
    {
        // Do nothing if the service has been already registered.
        if ($this->has($identifier)) {
            return;
        }

        $setUpData = $this->configuration[$identifier];

        // Create the definition.
        $definition = new Definition($serviceClass);

        // By default the Symfony DI shares all services. In WebHemi by default nothing is shared.
        if (!isset($setUpData[self::SERVICE_SHARE])) {
            $setUpData[self::SERVICE_SHARE] = false;
        }

        $definition->setShared((bool) $setUpData[self::SERVICE_SHARE]);

        // Register the service.
        $service = $this->container->setDefinition($identifier, $definition);

        // Add arguments.
        if (isset($setUpData[self::SERVICE_ARGUMENTS])) {
            foreach ($setUpData[self::SERVICE_ARGUMENTS] as $parameter) {
                $this->setServiceArgument($service, $parameter);
            }
        }

        // Register method callings.
        if (isset($setUpData[self::SERVICE_METHOD_CALL])) {
            foreach ($setUpData[self::SERVICE_METHOD_CALL] as $method => $parameterList) {
                // Check the parameter list for reference services
                foreach ($parameterList as &$parameter) {
                    $parameter = $this->getReferenceServiceIfAvailable($parameter);
                }

                $service->addMethodCall($method, $parameterList);
            }
        }
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
        if (is_string($classOrServiceName)) {
            if ($this->has($classOrServiceName)) {
                // The parameter is a registered service.
                $reference = new Reference($classOrServiceName);
            } elseif (isset($this->servicesToDefine[$classOrServiceName])) {
                // The parameter is defined as a service but it is not yet registered; alias is given.
                $this->registerService($classOrServiceName, $this->servicesToDefine[$classOrServiceName]);
                $reference = new Reference($classOrServiceName);
            } elseif (in_array($classOrServiceName, $this->servicesToDefine)) {
                // The parameter is defined as a service but it is not yet registered; real class is given.
                $referenceAlias = array_search($classOrServiceName, $this->servicesToDefine);
                $this->registerService($referenceAlias, $this->servicesToDefine[$referenceAlias]);
                $reference = new Reference($referenceAlias);
            } elseif (class_exists($classOrServiceName)) {
                // The parameter is not a service, but it is a class that can be instantiated. e.g.: DateTime::class
                $this->container->register($classOrServiceName, $classOrServiceName);
                $reference = new Reference($classOrServiceName);
            }
        }

        return $reference;
    }

    /**
     * Creates a safe normalized name.
     *
     * @param $className
     * @param $argumentName
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
     * Gets a service.
     *
     * @param string $identifier
     *
     * @return object
     */
    public function get($identifier)
    {
        return $this->container->get($identifier);
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
     * Sets service argument.
     *
     * @param string|Definition $service
     * @param mixed             $parameter
     *
     * @throws InitException
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function setServiceArgument($service, $parameter)
    {
        if ($this->container->initialized($service)) {
            throw new InitException('Cannot add argument to an already initialized service.');
        }

        if (!$service instanceof Definition) {
            $service = $this->container->getDefinition($service);
        }

        $serviceClass = $service->getClass();

        // Create a normalized name for the argument.
        if (!is_scalar($parameter)) {
            $normalizedName = $this->getNormalizedName($serviceClass, self::$parameterIndex++);
        } else {
            $normalizedName = $this->getNormalizedName($serviceClass, $parameter);
        }

        // Check if the parameter is a service.
        $parameter = $this->getReferenceServiceIfAvailable($parameter);

        $this->container->setParameter($normalizedName, $parameter);
        $service->addArgument('%'.$normalizedName.'%');

        return $this;
    }
}
