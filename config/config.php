<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
require_once __DIR__.'/functions.php';

// Set PHP settings according to the environment
set_environment();

// Start render stat logging
render_stat();

return get_full_config();
