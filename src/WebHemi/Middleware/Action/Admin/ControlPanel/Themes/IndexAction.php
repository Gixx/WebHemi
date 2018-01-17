<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Themes;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var EnvironmentInterface
     */
    private $environment;
    /**
     * @var array
     */
    private $defaultData = [
        'name' => null,
        'title' => null,
        'description' => '',
        'version' => '',
        'author' => 'Unknown',
        'homepage' => '',
        'license' => '',
        'read_only' => false,
        'feature_website' => false,
        'feature_login' => false,
        'feature_admin' => false,
        'logo' => '',
        'preview' => '',
    ];

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

            $themes[$themeName] = $this->defaultData;
            $themes[$themeName]['name'] = $themes[$themeName]['title'] = $themeName;
            $themes[$themeName]['read_only'] = isset($usedThemes[$themeName]);
            $themes[$themeName]['feature_website'] = (bool) ($themeData['features']['website_support'] ?? false);
            $themes[$themeName]['feature_login'] = (bool) ($themeData['features']['admin__login_support'] ?? false);
            $themes[$themeName]['feature_admin'] = (bool) ($themeData['features']['admin_support'] ?? false);

            foreach ($themeData['legal'] as $name => $value) {
                if (in_array($name, ['logo', 'preview'])) {
                    $value = $themeStaticPath.$value;
                }

                $themes[$themeName][$name] = $value;
            }
        }

        ksort($themes);

        return [
            'themes' => $themes,
            'progressId' => 'test'
        ];
    }
}
