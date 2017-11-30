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
 * Default Website and Admin application configuration.
 */
return [
    'applications' => [
        'website' => [
            'path'        => 'www',
            'theme'       => 'default',
            'type'        => 'domain',
            'locale'      => 'en_GB.UTF-8',
            'timezone'    => 'Europe/London',
        ],
        'admin' => [
            'path'        => 'admin',
            'theme'       => 'default',
            'type'        => 'directory',
            'locale'      => 'en_GB.UTF-8',
            'timezone'    => 'Europe/London',
        ],
    ],
];
