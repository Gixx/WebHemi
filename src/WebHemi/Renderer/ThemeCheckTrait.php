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

        if (!$this->configuration->has('themes/'.$selectedTheme)
            || !$this->checkSelectedThemeFeatures(
                $this->configuration->getConfig('themes/'.$selectedTheme),
                $this->environmentManager
            )
        ) {
            $selectedTheme = EnvironmentManager::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
        }

        return $selectedThemeResourcePath;
    }

    /**
     * Checks if the selected theme can be used with the current application.
     *
     * @param ConfigInterface    $themeConfig
     * @param EnvironmentManager $environmentManager
     * @return bool
     */
    protected function checkSelectedThemeFeatures(
        ConfigInterface $themeConfig,
        EnvironmentManager $environmentManager
    ) : bool {
        $canUseThisTheme = true;

        // check the theme settings
        // If no theme support for the application, then use the default theme
        if (($this->isAdminApplication($environmentManager, false) && !$this->isFeatureSupported($themeConfig, 'admin'))
            || (// check if admin login page but no admin login support
                $this->isAdminApplication($environmentManager, true)
                && !$this->isFeatureSupported($themeConfig, 'admin_login')
            ) || (// check if not admin page but no website support
                !$this->isAdminApplication($environmentManager, false)
                && !$this->isFeatureSupported($themeConfig, 'website')
            ) || (// check if not admin login page but no website login support
                !$this->isAdminApplication($environmentManager, true)
                && !$this->isFeatureSupported($themeConfig, 'website')
            )
        ) {
            $canUseThisTheme = false;
        }

        return $canUseThisTheme;
    }

    /**
     * Checks whether the current application is the Admin(login) or not.
     *
     * @param EnvironmentManager $environmentManager
     * @param bool               $checkIfLogin
     * @return bool
     */
    protected function isAdminApplication(EnvironmentManager $environmentManager, bool $checkIfLogin = false) : bool
    {
        $isAdmin = 'admin' == $environmentManager->getSelectedApplication();
        $isLogin = strpos($environmentManager->getRequestUri(), '/auth/login') !== false;

        return $checkIfLogin ? $isAdmin && $isLogin : $isAdmin && !$isLogin;
    }

    /**
     * Checks the config for feature settings.
     *
     * @param ConfigInterface $themeConfig
     * @param string          $feature
     * @return bool
     */
    protected function isFeatureSupported(ConfigInterface $themeConfig, string $feature) : bool
    {
        return $themeConfig->has('features/'.$feature.'_support')
            && (bool) $themeConfig->getData('features/'.$feature.'_support')[0];
    }
}
