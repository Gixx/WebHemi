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

namespace WebHemi\Renderer\ServiceAdapter\Twig;

use RuntimeException;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\FilterInterface;
use WebHemi\Renderer\HelperInterface;

/**
 * Class TwigExtension
 *
 * @codeCoverageIgnore - Test helpers and filters individually. It's only the shipped solution
 *                       to add them to the renderer.
 */
class TwigExtension extends Twig_Extension
{
    /** @var DependencyInjectionInterface */
    private $dependencyInjectionAdapter;
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var EnvironmentInterface */
    private $environmentManager;

    /**
     * TwigExtension constructor.
     */
    public function __construct()
    {
        // Oh, this is a disgusting ugly hack...
        global $dependencyInjection;
        $this->dependencyInjectionAdapter = $dependencyInjection;
        $this->configuration = $this->dependencyInjectionAdapter->get(ConfigurationInterface::class);
        $this->environmentManager = $this->dependencyInjectionAdapter->get(EnvironmentInterface::class);
    }

    /**
     * Returns extension filters.
     *
     * @return Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return $this->getExtensions('filter');
    }

    /**
     * Returns extension functions.
     *
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return $this->getExtensions('helper');
    }

    /**
     * Gets the specific type of extension
     *
     * @param string $type Must be `filter` or `helper`
     * @return array
     */
    private function getExtensions(string $type) : array
    {
        $extensions = [];
        $extensionConfig = $this->getConfig($type);

        foreach ($extensionConfig as $className) {
            $callable = $this->dependencyInjectionAdapter->get($className);
            $this->checkExtensionType($type, $callable);

            if ($type == 'helper') {
                $extensions[] = new Twig_SimpleFunction($callable::getName(), $callable, $callable::getOptions());
                continue;
            }

            /** FilterInterface $callable */
            $extensions[] = new Twig_SimpleFilter($callable::getName(), $callable, $callable::getOptions());
        }

        return $extensions;
    }

    /**
     * Checks whether the extension has the valid type.
     *
     * @param string $type
     * @param object $callable
     * @throws RuntimeException
     */
    private function checkExtensionType(string $type, $callable) : void
    {
        if (($type == 'helper' && $callable instanceof HelperInterface)
            || ($type == 'filter' && $callable instanceof FilterInterface)
        ) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'The class %s cannot be registered as Renderer/'.ucfirst($type).'!',
                get_class($callable)
            ),
            1000
        );
    }

    /**
     * Returns the renderer config by type.
     *
     * @param string $type
     * @return array
     */
    private function getConfig(string $type) : array
    {
        $module = $this->environmentManager->getSelectedModule();
        $config = [];

        if ($this->configuration->has('renderer/Global/'.$type)) {
            $config = $this->configuration->getData('renderer/Global/'.$type);
        }

        if ($this->configuration->has('renderer/'.$module.'/'.$type)) {
            $config = array_merge(
                $config,
                $this->configuration->getData('renderer/'.$module.'/'.$type)
            );
        }

        return $config;
    }
}
