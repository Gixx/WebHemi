<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

return [
    'logger' => [
        'access' => [
            'path' => __DIR__.'/../../../data/log/access',
            'file_name' => 'access-',
            'file_extension' => 'log',
            'date_format' => 'Y-m-d H:i:s.u',
            'log_level' => 3
        ],
        'event' => [
            'path' => __DIR__.'/../../../data/log/event',
            'file_name' => 'event-',
            'file_extension' => 'log',
            'date_format' => 'Y-m-d H:i:s.u',
            'log_level' => 1
        ]
    ],
];
