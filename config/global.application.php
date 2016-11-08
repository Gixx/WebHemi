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
 * Default Website and Admin application configuration.
 */
return [
    'applications' => [
        'website' => [
            'path'        => 'www',
            'theme'       => 'default',
        ],
        'admin' => [
            'path'        => 'admin',
            'theme'       => 'default',
            'type'        => 'directory',
        ],
    ],
];
