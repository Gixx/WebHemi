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

namespace WebHemi\Adapter\Renderer\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Adapter\Renderer\RendererFilterInterface;
use WebHemi\Adapter\Renderer\RendererHelperInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

/**
 * Class TwigExtension
 *
 * @codeCoverageIgnore - Test helpers and filters individually. It's only the shipped solution
 *                       to add them to the renderer.
 */
class TwigExtension extends Twig_Extension
{
    /** @var DependencyInjectionAdapterInterface */
    private $dependencyInjectionAdapter;
    /** @var ConfigInterface */
    private $configuration;
    /** @var EnvironmentManager */
    private $environmentManager;

    /**
     * WebHemiTwigExtension constructor.
     */
    public function __construct()
    {
        // Oh, this is a disgusting ugly hack...
        global $diAdapter;
        $this->dependencyInjectionAdapter = $diAdapter;
        $this->configuration = $diAdapter->get(ConfigInterface::class);
        $this->environmentManager = $diAdapter->get(EnvironmentManager::class);
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
            /** @var RendererFilterInterface $callable */
            $callable = $this->dependencyInjectionAdapter->get($className);
            $filters[] = new Twig_SimpleFilter($callable::getName(), $callable);
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
            /** @var RendererHelperInterface $callable */
            $callable = $this->dependencyInjectionAdapter->get($className);
            $functions[] = new Twig_SimpleFunction($callable::getName(), $callable);
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
