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

namespace WebHemi\Renderer;

use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

/**
 * Class ThemeCheckTrait
 */
trait ThemeCheckTrait
{
    /** @var ConfigInterface */
    protected $configuration;
    /** @var EnvironmentManager */
    protected $environmentManager;
    /** @var ConfigInterface */
    private $themeConfig;

    /**
     * Checks if the selected theme supports the current state and returns the correct resource path.
     *
     * @param string $selectedTheme
     * @return string
     */
    protected function getSelectedThemeResourcePath(string &$selectedTheme) : string
    {
        $selectedTheme = $this->environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $this->environmentManager->getResourcePath();

        // Temporary, only can access by this trait.
        $this->themeConfig = $this->configuration->getConfig('themes/'.$selectedTheme);

        if (!$this->configuration->has('themes/'.$selectedTheme) || !$this->checkSelectedThemeFeatures()) {
            $selectedTheme = EnvironmentManager::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
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
        if (($this->isAdminApplication() && !$this->isFeatureSupported($this->themeConfig, 'admin')
            ) || (// check if admin login page but no admin login support
                $this->isAdminLoginPage() && !$this->isFeatureSupported($this->themeConfig, 'admin_login')
            ) || (// check if not admin page but no website support
                $this->isWebsiteApplication() && !$this->isFeatureSupported($this->themeConfig, 'website')
            )
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
        return strpos($this->environmentManager->getRequestUri(), '/auth/login') !== false;
    }

    /**
     * Checks whether the current application belongs to the Admin module or not.
     *
     * @return bool
     */
    private function isAdminApplication() : bool
    {
        return !$this->isAdminLoginPage() && 'Admin' == $this->environmentManager->getSelectedModule();
    }

    /**
     * Checks whether the current application belongs to any Website module application.
     *
     * @return bool
     */
    private function isWebsiteApplication()
    {
        return 'Website' == $this->environmentManager->getSelectedModule();
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
