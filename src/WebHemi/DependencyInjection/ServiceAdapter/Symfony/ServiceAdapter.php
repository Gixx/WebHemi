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

use Exception;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface;
use WebHemi\DependencyInjection\ServiceAdapter\AbstractAdapter;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractAdapter
{
    /** @var ContainerBuilder */
    private $container;
    /** @var int */
    private static $parameterIndex = 0;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        parent::__construct($configuration);

        $this->container = new ContainerBuilder();
    }

    /**
     * Returns true if the given service is registered.
     *
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier) : bool
    {
        return $this->container->has($identifier)
            || isset($this->serviceLibrary[$identifier])
            || class_exists($identifier);
    }

    /**
     * Gets a service.
     *
     * @param string $identifier
     * @return object
     */
    public function get(string $identifier)
    {
        // Not registered but valid class name, so register it
        if (!isset($this->serviceLibrary[$identifier]) && class_exists($identifier)) {
            $this->registerService($identifier);
        }

        // The service is registered in the library but not in the container, so register it into the container too.
        if (!$this->container->has($identifier)) {
            $this->registerServiceToContainer($identifier);
        }

        $service = $this->container->get($identifier);
        $this->serviceLibrary[$identifier][self::SERVICE_INITIALIZED] = true;

        return $service;
    }

    /**
     * Registers the service into the container.
     *
     * @param string $identifier
     * @return ServiceAdapter
     */
    private function registerServiceToContainer(string $identifier) : ServiceAdapter
    {
        // At this point the service must be in the library
        if (!isset($this->serviceLibrary[$identifier])) {
            throw new InvalidArgumentException(
                sprintf('Invalid service name: %s', $identifier),
                1000
            );
        }

        // Create the definition.
        $definition = new Definition($this->serviceLibrary[$identifier][self::SERVICE_CLASS]);
        $definition->setShared($this->serviceLibrary[$identifier][self::SERVICE_SHARE]);

        // Register the service in the container.
        $service = $this->container->setDefinition($identifier, $definition);

        // Add arguments.
        $argumentList = $this->setArgumentListReferences($this->serviceLibrary[$identifier][self::SERVICE_ARGUMENTS]);
        foreach ($argumentList as $parameter) {
            // Create a normalized name for the argument.
            $serviceClass = $this->serviceLibrary[$identifier][self::SERVICE_CLASS];
            $normalizedName = $this->getNormalizedName($serviceClass, $parameter);
            $this->container->setParameter($normalizedName, $parameter);
            $service->addArgument('%'.$normalizedName.'%');
        }

        // Register method callings.
        foreach ($this->serviceLibrary[$identifier][self::SERVICE_METHOD_CALL] as $methodCallList) {
            $method = $methodCallList[0];
            $argumentList = $this->setArgumentListReferences($methodCallList[1] ?? []);
            $service->addMethodCall($method, $argumentList);
        }

        return $this;
    }

    /**
     * Tries to identify referce services in the argument list.
     *
     * @param array $argumentList
     * @return array
     */
    private function setArgumentListReferences(array $argumentList) : array
    {
        foreach ($argumentList as $key => &$value) {
            // Associative array keys marks literal values
            if (!is_numeric($key)) {
                continue;
            }

            // Try to get the service. If exists (or can be registered), then it can be referenced too.
            try {
                $this->get($value);
                $value = new Reference($value);
            } catch (Exception $e) {
                // Not a valid service: no action, go on;
                continue;
            }
        }

        return $argumentList;
    }

    /**
     * Creates a safe normalized name.
     *
     * @param string $className
     * @param mixed $parameter
     * @return string
     */
    private function getNormalizedName(string $className, $parameter) : string
    {
        $parameterName = !is_scalar($parameter) ? self::$parameterIndex++ : $parameter;

        $className = 'C_'.preg_replace('/[^a-z0-9]/', '', strtolower($className));
        $parameterName = 'A_'.preg_replace('/[^a-z0-9]/', '', strtolower((string) $parameterName));

        return $className.'.'.$parameterName;
    }

    /**
     * Register the service object instance.
     *
     * @param string  $identifier
     * @param object  $serviceInstance
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

            $this->container->register($identifier)
                ->setShared(true)
                ->setSynthetic(true);

            $this->container->set($identifier, $serviceInstance);

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
