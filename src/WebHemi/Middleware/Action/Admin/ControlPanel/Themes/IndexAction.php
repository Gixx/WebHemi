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

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Themes;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var EnvironmentInterface */
    private $environment;

    /**
     * IndexAction constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environment
     */
    public function __construct(ConfigurationInterface $configuration, EnvironmentInterface $environment)
    {
        $this->configuration = $configuration;
        $this->environment = $environment;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-control-panel-themes-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $themes = $usedThemes = [];

        foreach ($this->configuration->getData('applications') as $application) {
            $usedThemes[$application['theme']] = $application['theme'];
        }

        // Make the default theme read-only.
        $usedThemes['default'] = 'default';

        foreach ($this->configuration->getData('themes') as $themeName => $themeData) {
            $themeStaticPath = '/resources/'.
                ($themeName == 'default' ? 'default_theme' : 'vendor_themes/'.$themeName)
                . '/static/';


            $themes[$themeName] = [
                'name' => $themeName,
                'title' => $themeData['legal']['title'] ?? $themeName,
                'description' => $themeData['legal']['description'] ?? '',
                'version' => $themeData['legal']['version'] ?? '',
                'author' => $themeData['legal']['author'] ?? 'Unknown',
                'homepage' => $themeData['legal']['homepage'] ?? '',
                'license' => $themeData['legal']['license'] ?? '',
                'read_only' => isset($usedThemes[$themeName]),
                'feature_website' => isset($themeData['features']['website_support'])
                    ? (bool) $themeData['features']['website_support']
                    : false,
                'feature_login' => isset($themeData['features']['admin_login_support'])
                    ? (bool) $themeData['features']['admin_login_support']
                    : false,
                'feature_admin' => isset($themeData['features']['admin_support'])
                    ? (bool) $themeData['features']['admin_support']
                    : false,
                'logo' => isset($themeData['legal']['logo'])
                    ? $themeStaticPath.$themeData['legal']['logo']
                    : '',
                'preview' => isset($themeData['legal']['preview'])
                    ? $themeStaticPath.$themeData['legal']['preview']
                    : '',
            ];
        }

        ksort($themes);

        return [
            'themes' => $themes,
            'progressId' => 'test'
        ];
    }
}
