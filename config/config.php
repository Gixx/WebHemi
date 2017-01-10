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
require_once __DIR__.'/functions.php';

render_stat();

return [
    'applications' => get_application_config(),
    'auth' => get_auth_config(),
    'dependencies' => get_dependencies_config(),
    'logging' => get_logging_cofig(),
    'middleware_pipeline' => get_pipeline_config(),
    'modules' => get_module_config(),
    'session' => get_session_config(),
    'themes' => get_theme_config(),
];
