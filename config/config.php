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

// Set PHP settings according to the environment
set_environment();

// Start render stat logging
render_stat();

return [
    'applications' => get_application_config(),
    'auth' => get_auth_config(),
    'dependencies' => get_dependencies_config(),
    'logger' => get_logger_config(),
    'middleware_pipeline' => get_pipeline_config(),
    'renderer' => get_renderer_config(),
    'router' => get_router_config(),
    'session' => get_session_config(),
    'themes' => get_theme_config(),
];
