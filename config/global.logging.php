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

return [
    'logging' => [
        'access' => [
            'path' => __DIR__.'/../data/log',
            'file_name' => 'access-',
            'file_extension' => 'log',
            'date_format' => 'Y-m-d H:i:s.u',
            'log_level' => 0
        ],
        'event' => [
            'path' => __DIR__.'/../data/log',
            'file_name' => 'event-',
            'file_extension' => 'log',
            'date_format' => 'Y-m-d H:i:s.u',
            'log_level' => 3
        ]
    ],
];
