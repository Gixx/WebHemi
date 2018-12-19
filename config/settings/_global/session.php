<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

return [
    'session' => [
        'namespace' => 'WebHemi',
        'cookie_prefix' => 'atsn',
        'session_name_salt' => 'WebHemi',
        'hash_function' => 'sha256',
        'use_only_cookies' => true,
        'use_cookies' => true,
        'use_trans_sid' => false,
        'cookie_http_only' => true,
        'save_path' => __DIR__.'/../../../data/session/',
    ],
];
