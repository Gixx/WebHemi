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
        'admin' => [
            'title'       => 'WebHemi Administration',
            'description' => 'A simple content management area for authorized persons only.',
            'type'        => 'directory',
            'path'        => 'admin',
            'theme'       => 'default',
        ],
        'website' => [
            'title'       => 'WebHemi Blog',
            'description' => 'A web application for those who likes simplicity and for developers who like clean code.',
            'theme'       => 'default',
        ],
    ]
];
