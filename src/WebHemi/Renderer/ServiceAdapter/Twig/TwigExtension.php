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
     * @return array<Twig_SimpleFilter>
     */
    public function getFilters()
    {
        $filters = [];
        $filterConfig = $this->getConfig('filter');

        foreach ($filterConfig as $className) {
            /** @var FilterInterface $callable */
            $callable = $this->dependencyInjectionAdapter->get($className);
            if ($callable instanceof FilterInterface) {
                $filters[] = new Twig_SimpleFilter($callable::getName(), $callable);
            } else {
                throw new RuntimeException(
                    sprintf(
                        'The class %s cannot be registered as Renderer/Filter!',
                        get_class($callable)
                    ),
                    1000
                );
            }
        }

        return $filters;
    }

    /**
     * Returns extension functions.
     *
     * @return array<Twig_SimpleFunction>
     */
    public function getFunctions() : array
    {
        $functions = [];
        $helperConfig = $this->getConfig('helper');

        foreach ($helperConfig as $className) {
            /** @var HelperInterface $callable */
            $callable = $this->dependencyInjectionAdapter->get($className);
            if ($callable instanceof HelperInterface) {
                $functions[] = new Twig_SimpleFunction($callable::getName(), $callable);
            } else {
                throw new RuntimeException(
                    sprintf(
                        'The class %s cannot be registered as Renderer/Helper!',
                        get_class($callable)
                    ),
                    1001
                );
            }
        }

        return $functions;
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
            $config = $this->configuration->getData('renderer/Global/' . $type);
        }

        if ($this->configuration->has('renderer/'.$module.'/'.$type)) {
            $config = merge_array_overwrite(
                $config,
                $this->configuration->getData('renderer/'.$module.'/'.$type)
            );
        }

        return $config;
    }
}
