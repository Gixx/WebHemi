<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

/**
 * Sets PHP settings according to the environment
 *
 * @return void
 */
function set_environment()
{
    if (!getenv('APPLICATION_ENV')) {
        putenv('APPLICATION_ENV=live');
    }

    if ('dev' == getenv('APPLICATION_ENV')) {
        error_reporting(E_ALL);
        ini_set('display_errors', 'on');
        ini_set('xdebug.var_display_max_depth', 10);
    }
}

/**
 * Collects and returns some information about the render time. First call will start start, the others will return.
 *
 * @return array
 */
function render_stat() : array
{
    return \WebHemi\GeneralLib::renderStat();
}

/**
 * Merge config arrays in the correct way.
 * This rewrites the given key->value pairs and does not make key->array(value1, value2) like the
 * `array_merge_recursive` does.
 *
 * @return array
 *
 * @throws InvalidArgumentException
 */
function merge_array_overwrite()
{
    $arguments = func_get_args();

    return forward_static_call_array([\WebHemi\GeneralLib::class, 'mergeArrayOverwrite'], $arguments);
}

/**
 * Reads the module config directory and returns the config.
 *
 * @param string $path
 * @return array
 */
function compose_config($path = 'modules')
{
    $config = [];
    $path = trim($path, '/');

    $configPath = realpath(__DIR__.'/'.$path);
    $entries = glob($configPath.'/*');

    foreach ($entries as $entry) {
        if (is_file($entry) && preg_match('/.*\.php$/', $entry)) {
            $entryConfig = require $entry;
            $config = merge_array_overwrite($config, $entryConfig);
        } elseif (is_dir($entry)) {
            $modulePath = str_replace(__DIR__, '', $entry);
            $config = merge_array_overwrite($config, compose_config($modulePath));
        }
    }

    return $config;
}

/**
 * Gets the full config
 *
 * @return array
 */
function get_full_config()
{
    static $config;

    if (!isset($config)) {
        $settingsConfig = compose_config('settings');
        $modulesConfig = compose_config('modules');

        $config = merge_array_overwrite($settingsConfig, $modulesConfig);

        $readOnlyApplications = [
            'admin',
            'website'
        ];
        $readOnlyApplicationConfig = [
            'applications' => [
                'website' => [
                    'module' => 'Website',
                    'path'   => '/',
                    'type'   => 'domain',
                ],
                'admin' => [
                    'module' => 'Admin',
                ],
            ],
        ];

        $config = merge_array_overwrite($config, $readOnlyApplicationConfig);

        // ensure that nobody plays with the modules
        foreach ($config['applications'] as $application => &$settings) {
            if (!in_array($application, $readOnlyApplications)) {
                $settings['module'] = 'Website';
            }
        }

        // It is important that the custom application should be checked first, then the 'admin', and the 'website' last
        $config['applications'] = array_reverse($config['applications']);

        // Add theme config from actual installed themes
        $config['themes'] = get_theme_config();
    }

    return $config;
}

/**
 * Reads and parses all the available theme configs.
 *
 * @return array
 */
function get_theme_config()
{
    $themeConfig = [
        'themes' => []
    ];
    $defaultThemeConfig = file_get_contents(__DIR__.'/../resources/default_theme/config.json');

    $themeConfig['themes']['default'] = json_decode($defaultThemeConfig, true);

    $vendorThemePath = realpath(__DIR__.'/../resources/vendor_themes');
    $handle = opendir($vendorThemePath);

    if (!$handle) {
        return $themeConfig['themes'];
    }

    while (false !== ($entry = readdir($handle))) {
        if (is_dir($vendorThemePath.'/'.$entry) && file_exists($vendorThemePath.'/'.$entry.'/config.json')) {
            $vendorThemeConfig = file_get_contents($vendorThemePath.'/'.$entry.'/config.json');
            $themeConfig['themes'][$entry] = @json_decode($vendorThemeConfig, true);
        }
    }
    closedir($handle);
    return $themeConfig['themes'];
}
