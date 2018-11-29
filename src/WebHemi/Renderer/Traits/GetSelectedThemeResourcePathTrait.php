<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Renderer\Traits;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;

/**
 * Class GetSelectedThemeResourcePathTrait
 */
trait GetSelectedThemeResourcePathTrait
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;
    /**
     * @var ConfigurationInterface
     */
    private $themeConfig;

    /**
     * Checks if the selected theme supports the current state and returns the correct resource path.
     *
     * @param  string                 $selectedTheme
     * @param  ConfigurationInterface $configuration
     * @param  EnvironmentInterface   $environmentManager
     * @return string
     */
    protected function getSelectedThemeResourcePath(
        string&$selectedTheme,
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager
    ) : string {
        $this->environment = $environmentManager;

        //$selectedTheme = $this->environment->getSelectedTheme();
        $selectedThemeResourcePath = $this->environment->getResourcePath();

        // Reset selected theme, if it's not found.
        if (!$configuration->has('themes/'.$selectedTheme)) {
            $selectedTheme = EnvironmentInterface::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentInterface::DEFAULT_THEME_RESOURCE_PATH;
        }

        // Temporary, only can access by this trait.
        $this->themeConfig = $configuration->getConfig('themes/'.$selectedTheme);

        // Reset selected theme, if it doesn't support the currenct application/page.
        if (!$this->checkSelectedThemeFeatures()) {
            $selectedTheme = EnvironmentInterface::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentInterface::DEFAULT_THEME_RESOURCE_PATH;
        }

        return $selectedThemeResourcePath;
    }

    /**
     * Checks if the selected theme can be used with the current application.
     *
     * @return bool
     */
    private function checkSelectedThemeFeatures() : bool
    {
        $canUseThisTheme = true;

        // check the theme settings
        // If no theme support for the application, then use the default theme
        if (($this->isAdminApplication() && !$this->isFeatureSupported('admin'))
            || ($this->isAdminLoginPage() && !$this->isFeatureSupported('admin_login'))
            || ($this->isWebsiteApplication() && !$this->isFeatureSupported('website'))
        ) {
            $canUseThisTheme = false;
        }

        return $canUseThisTheme;
    }

    /**
     * Checks whether the current application belongs to the Admin module and the request calls the login page.
     *
     * @return bool
     */
    private function isAdminLoginPage() : bool
    {
        return strpos($this->environment->getRequestUri(), '/auth/login') !== false;
    }

    /**
     * Checks whether the current application belongs to the Admin module or not.
     *
     * @return bool
     */
    private function isAdminApplication() : bool
    {
        return !$this->isAdminLoginPage() && 'Admin' == $this->environment->getSelectedModule();
    }

    /**
     * Checks whether the current application belongs to any Website module application.
     *
     * @return bool
     */
    private function isWebsiteApplication() : bool
    {
        return 'Website' == $this->environment->getSelectedModule();
    }

    /**
     * Checks the config for feature settings.
     *
     * @param  string $feature
     * @return bool
     */
    private function isFeatureSupported(string $feature) : bool
    {
        return $this->themeConfig->has('features/'.$feature.'_support')
            && (bool) $this->themeConfig->getData('features/'.$feature.'_support')[0];
    }
}
