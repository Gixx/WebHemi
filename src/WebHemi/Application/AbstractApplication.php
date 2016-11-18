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
namespace WebHemi\Application;

use InvalidArgumentException;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;

/**
 * Class AbstractApplication.
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /** @var DependencyInjectionAdapterInterface */
    private $container;

    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     */
    public function __construct(DependencyInjectionAdapterInterface $container)
    {
        $this->container = $container;

        // Final touches.
        $this->prepareContainer();
    }

    /**
     * Get ready to run the application: set final data for specific services.
     *
     * @codeCoverageIgnore - Check the EnvironmentManager and Container adapter tests.
     */
    private function prepareContainer()
    {
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $this->container->get(EnvironmentManager::class);

        // Set proper arguments for the HTTP adapter.
        $this->container
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $environmentManager->getEnvironmentData('GET')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $environmentManager->getEnvironmentData('POST')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $environmentManager->getEnvironmentData('SERVER')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $environmentManager->getEnvironmentData('COOKIE')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $environmentManager->getEnvironmentData('FILES')
            );

        try {
            $themeConfig = $environmentManager
                ->getApplicationTemplateSettings($environmentManager->getSelectedTheme());
            $themeResourcePath = $environmentManager->getResourcePath();
        } catch (InvalidArgumentException $e) {
            $themeConfig = $environmentManager->getApplicationTemplateSettings(EnvironmentManager::DEFAULT_THEME);
            $themeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
        }

        // Set proper arguments for the renderer.
        $this->container
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $themeConfig
            )
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $themeResourcePath
            )
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $environmentManager->getSelectedApplicationUri()
            );

        // Set proper arguments for the router.
        $this->container
            ->setServiceArgument(
                RouterAdapterInterface::class,
                $environmentManager->getModuleRouteSettings()
            )
            ->setServiceArgument(
                RouterAdapterInterface::class,
                $environmentManager->getSelectedApplicationUri()
            );
    }

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    final public function getContainer()
    {
        return $this->container;
    }

   /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    abstract public function run();
}
