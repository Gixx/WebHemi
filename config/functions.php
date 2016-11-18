<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

/**
 * Damn var dumping in a user-friendly way.
 *
 * @param mixed $variable
 */
function d(...$variables)
{
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $FILE = __FILE__;
    $LINE = __LINE__;
    if (isset($backtrace[0])) {
        $FILE = $backtrace[0]['file'];
        $LINE = $backtrace[0]['line'];
    }
    if (php_sapi_name() !== 'cli') {
        echo '<strong>In file '.$FILE." at line ".$LINE.':</strong>';
    } else {
        echo 'In file '.$FILE." at line ".$LINE.':'.PHP_EOL;
    }
    var_dump(...$variables);
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
    if (func_num_args() < 2) {
        throw new \InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' needs two or more array arguments');
    }
    $arrays = func_get_args();
    $merged = [];

    while ($arrays) {
        $array = array_shift($arrays);
        if (!is_array($array)) {
            throw new \InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' encountered a non array argument');
        }
        if (!$array) {
            continue;
        }
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = merge_array_overwrite($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            } else {
                $merged[] = $value;
            }
        }
    }
    return $merged;
}

/**
 * Gets the database config if exists.
 *
 * @return array
 */
function get_pdo_config()
{
    $databaseConfig = [];

    if (file_exists(__DIR__.'/local.db.php')) {
        $databaseConfig = require __DIR__.'/local.db.php';
    }

    return $databaseConfig['pdo'];
}

/**
 * Gets the application config by combine the default, a custom and the read-only settings.
 *
 * @return array
 *
 * @throws Exception
 */
function get_application_config()
{
    $defaultApplicationConfig = require __DIR__.'/global.application.php';
    $localApplicationConfig = [];
    $readOnlyApplications = [
        'admin',
        'website'
    ];
    $readOnlyApplicationConfig = [
        'applications' => [
            'website' => [
                'module'      => 'Website',
                'type'        => 'domain',
            ],
            'admin' => [
                'module'      => 'Admin',
            ],
        ],
    ];

    if (file_exists(__DIR__.'/local.application.php')) {
        $localApplicationConfig = require __DIR__.'/local.application.php';
    }

    $applicationConfig = merge_array_overwrite(
        $defaultApplicationConfig,
        $localApplicationConfig,
        $readOnlyApplicationConfig
    );

    // ensure that nobody plays with the modules
    foreach ($applicationConfig['applications'] as $application => &$settings) {
        if (!in_array($application, $readOnlyApplications)) {
            $settings['module'] = 'Website';
        }
    }

    // It is important that the custom application should be checked first, then the 'admin', and the 'website' last.
    $applicationConfig['applications'] = array_reverse($applicationConfig['applications']);

    return $applicationConfig['applications'];
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

/**
 * Reads the module config directory and returns the config.
 *
 * @return array
 */
function get_full_module_config()
{
    $moduleConfig = [];

    $moduleConfigPath = realpath(__DIR__.'/modules');
    $handle = opendir($moduleConfigPath);

    if (!$handle) {
        return $moduleConfig;
    }

    while (false !== ($entry = readdir($handle))) {
        if (is_file($moduleConfigPath.'/'.$entry)) {
            $config = require $moduleConfigPath.'/'.$entry;

            $moduleConfig = merge_array_overwrite($moduleConfig, $config);
        }
    }
    closedir($handle);
    return $moduleConfig;
}

/**
 * Returns the module config.
 *
 * @return array
 */
function get_module_config()
{
    $moduleConfig = get_full_module_config();

    return $moduleConfig['modules'];
}

/**
 * Returns the dependencies config.
 *
 * @return array
 */
function get_dependencies_config()
{
    $moduleConfig = get_full_module_config();
    $globalConfig = require __DIR__.'/global.dependencies.php';

    $dependenciesConfig = merge_array_overwrite($moduleConfig, $globalConfig);

    return $dependenciesConfig['dependencies'];
}

/**
 * Returns the pipeline config.
 *
 * @return array
 */
function get_pipeline_config()
{
    $moduleConfig = get_full_module_config();
    $globalConfig = require __DIR__.'/global.pipeline.php';

    $dependenciesConfig = merge_array_overwrite($moduleConfig, $globalConfig);

    return $dependenciesConfig['middleware_pipeline'];
}

/**
 * Returns the auth config.
 *
 * @return array
 */
function get_auth_config()
{
    $globalAuthConfig = require __DIR__.'/global.auth.php';
    $localAuthConfig = (file_exists(__DIR__.'/local.auth.php')) ? require __DIR__.'/local.auth.php' : [];

    $authConfig = merge_array_overwrite($globalAuthConfig, $localAuthConfig);

    return $authConfig['auth'];
}

/**
 * Returns the session config.
 *
 * @return array
 */
function get_session_config()
{
    $globalSessionConfig = require __DIR__.'/global.session.php';
    $localSessionConfig = (file_exists(__DIR__.'/local.session.php')) ? require __DIR__.'/local.session.php' : [];

    $sessionConfig = merge_array_overwrite($globalSessionConfig, $localSessionConfig);

    return $sessionConfig['session'];
}
