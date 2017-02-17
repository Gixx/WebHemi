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

/**
 * Damn var dumping in a user-friendly way.
 *
 * @param array $variables
 *
 * @internal
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
 * Collects and returns some information about the render time. First call will start start, the others will return.
 *
 * @return array
 */
function render_stat() : array
{
    static $stat;

    // Set timer
    if (!isset($stat)) {
        $stat = [
            'start_time' => microtime(true),
            'end_time' => null,
            'duration' => 0,
            'memory' => 0
        ];

        return $stat;
    }

    // Get time
    $stat['end_time'] = microtime(true);
    $stat['duration'] = bcsub($stat['end_time'], $stat['start_time'], 4);

    // Memory peak
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max(memory_get_peak_usage(true), 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    $stat['memory'] = round($bytes, 2) . ' ' . $units[$pow];

    return $stat;
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
 * Gets the application config by combine the default, a custom and the read-only settings.
 *
 * @return array
 *
 * @throws Exception
 */
function get_application_config()
{
    $defaultApplicationConfig = require __DIR__.'/settings/global/application.php';
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

    if (file_exists(__DIR__ . '/settings/local/application.php')) {
        $localApplicationConfig = require __DIR__ . '/settings/local/application.php';
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
 * @param string $path
 * @return array
 */
function get_full_module_config($path = 'modules')
{
    $moduleConfig = [];
    $path = trim($path, '/');

    $moduleConfigPath = realpath(__DIR__.'/'.$path);
    $handle = opendir($moduleConfigPath);

    if (!$handle) {
        return $moduleConfig;
    }

    while (false !== ($entry = readdir($handle))) {
        if (in_array($entry, ['.', '..'])) {
            continue;
        }

        if (is_file($moduleConfigPath.'/'.$entry)) {
            $config = require $moduleConfigPath.'/'.$entry;
            $moduleConfig = merge_array_overwrite($moduleConfig, $config);
        } elseif (is_dir($moduleConfigPath.'/'.$entry)) {
            $moduleConfig = merge_array_overwrite($moduleConfig, get_full_module_config($path.'/'.$entry));
        }
    }
    closedir($handle);
    return $moduleConfig;
}

/**
 * Returns the routing config.
 *
 * @return array
 */
function get_routing_config()
{
    $moduleConfig = get_full_module_config();

    return $moduleConfig['routing'];
}

/**
 * Returns the dependencies config.
 *
 * @return array
 */
function get_dependencies_config()
{
    $moduleConfig = get_full_module_config();

    if (empty($moduleConfig['dependencies']['Global'][\WebHemi\Adapter\Data\DataDriverInterface::class])
        && file_exists(__DIR__.'/settings/local/db.php')
    ) {
        $dataDriverConfig = require __DIR__.'/settings/local/db.php';
        $moduleConfig = merge_array_overwrite($moduleConfig, $dataDriverConfig);
    }

    return $moduleConfig['dependencies'];
}

/**
 * Returns the pipeline config.
 *
 * @return array
 */
function get_pipeline_config()
{
    $moduleConfig = get_full_module_config();

    return $moduleConfig['middleware_pipeline'];
}

/**
 * Returns the auth config.
 *
 * @return array
 */
function get_auth_config()
{
    $globalAuthConfig = require __DIR__.'/settings/global/auth.php';
    $localAuthConfig = (file_exists(__DIR__.'/settings/local/auth.php'))
        ? require __DIR__.'/settings/local/auth.php'
        : [];

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
    $globalSessionConfig = require __DIR__.'/settings/global/session.php';
    $localSessionConfig = (file_exists(__DIR__.'/settings/local/session.php'))
        ? require __DIR__.'/settings/local/session.php'
        : [];

    $sessionConfig = merge_array_overwrite($globalSessionConfig, $localSessionConfig);

    return $sessionConfig['session'];
}

/**
 * Returns the logging config.
 *
 * @return mixed
 */
function get_logging_cofig()
{
    $globalLoggingConfig = require __DIR__.'/settings/global/logging.php';
    $localLoggingConfig = (file_exists(__DIR__.'/settings/local/logging.php'))
        ? require __DIR__.'/settings/local/logging.php'
        : [];

    $loggingConfig = merge_array_overwrite($globalLoggingConfig, $localLoggingConfig);

    return $loggingConfig['logging'];
}
