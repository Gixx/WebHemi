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

/**
 * Default Website and Admin application configuration.
 */
return [
    'applications' => [
        'website' => [
            'domain'   => $_SERVER['SERVER_NAME'],
            'path'     => '/',
            'type'     => 'domain',
            'theme'    => 'default',
            'locale'   => 'en_GB.UTF-8',
            'timezone' => 'Europe/London',
        ],
        'admin' => [
            'domain'   => $_SERVER['SERVER_NAME'],
            'path'     => '/admin',
            'type'     => 'directory',
            'theme'    => 'default',
            'locale'   => 'en_GB.UTF-8',
            'timezone' => 'Europe/London',
        ],
    ],
];
