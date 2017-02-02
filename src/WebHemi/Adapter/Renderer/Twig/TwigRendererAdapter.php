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

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

/**
 * Class TwigRendererAdapter.
 */
class TwigRendererAdapter implements RendererAdapterInterface
{
    /** @var Twig_Environment */
    private $adapter;
    /** @var ConfigInterface */
    private $configuration;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var string */
    private $defaultViewPath;
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $templateResourcePath;
    /** @var string */
    private $applicationBaseUri;

    /**
     * RendererAdapterInterface constructor.
     *
     * @param ConfigInterface    $configuration
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ConfigInterface $configuration, EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
        $documentRoot = $environmentManager->getDocumentRoot();
        $selectedTheme = $environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $environmentManager->getResourcePath();

        if (!$configuration->has('themes/'.$selectedTheme)
            || !$this->checkSelectedThemeFeatures($configuration->getConfig('themes/'.$selectedTheme))
        ) {
            $selectedTheme = EnvironmentManager::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
        }

        $this->configuration = $configuration->getConfig('themes/'.$selectedTheme);

        $this->defaultViewPath = $documentRoot.EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH.'/view';
        $this->templateViewPath = $documentRoot.$selectedThemeResourcePath.'/view';
        $this->templateResourcePath = $selectedThemeResourcePath.'/static';
        $this->applicationBaseUri = $environmentManager->getSelectedApplicationUri();

        $loader = new Twig_Loader_Filesystem($this->templateViewPath);
        $loader->addPath($this->defaultViewPath, 'WebHemi');
        $loader->addPath($this->templateViewPath, 'Theme');

        $this->adapter = new Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $this->adapter->addExtension(new Twig_Extension_Debug());
        $this->adapter->addExtension(new WebHemiTwigExtension($this->templateViewPath));
    }

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     * @throws InvalidArgumentException
     * @return StreamInterface
     */
    public function render(string $template, array $parameters = []) : StreamInterface
    {
        if ($this->configuration->has('map/'.$template)) {
            $template = $this->configuration->getData('map/'.$template)[0];
        }

        if (!file_exists($this->templateViewPath.'/'.$template)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to render file: "%s". No such file: %s.',
                    $template,
                    $this->templateViewPath.'/'.$template
                )
            );
        }

        // Tell the template where the resources are.
        $parameters['template_resource_path'] = $this->templateResourcePath;
        $parameters['application_base_uri'] = $this->applicationBaseUri;

        $output = $this->adapter->render($template, $parameters);

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }

    /**
     * Checks if the selected theme can be used with the current application.
     *
     * @param ConfigInterface $themeConfig
     * @return bool
     */
    private function checkSelectedThemeFeatures(ConfigInterface $themeConfig) : bool
    {
        $canUseThisTheme = true;

        // check the theme settings
        // If no theme support for the application, then use the default theme
        if (($this->isAdminApplication(false) && !$this->isFeatureSupported($themeConfig, 'admin'))
            || ($this->isAdminApplication(true) && !$this->isFeatureSupported($themeConfig, 'admin_login'))
            || (!$this->isAdminApplication(false) && !$this->isFeatureSupported($themeConfig, 'website'))
            || (!$this->isAdminApplication(true) && !$this->isFeatureSupported($themeConfig, 'website'))
        ) {
            $canUseThisTheme = false;
        }

        return $canUseThisTheme;
    }

    /**
     * Checks whether the current application is the Admin(login) or not.
     *
     * @param bool $checkIfLogin
     * @return bool
     */
    private function isAdminApplication(bool $checkIfLogin = false) : bool
    {
        $isAdmin = 'admin' == $this->environmentManager->getSelectedApplication();
        $isLogin = strpos($this->environmentManager->getRequestUri(), '/auth/login') !== false;

        return $checkIfLogin ? $isAdmin && $isLogin : $isAdmin && !$isLogin;
    }

    /**
     * Checks the config for feature settings.
     *
     * @param ConfigInterface $themeConfig
     * @param string          $feature
     * @return bool
     */
    private function isFeatureSupported(ConfigInterface $themeConfig, string $feature) : bool
    {
        return $themeConfig->has('features/'.$feature.'_support')
            && (bool) $themeConfig->getData('features/'.$feature.'_support')[0];
    }
}
