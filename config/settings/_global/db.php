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
use WebHemi\Data;

return [
    'dependencies' => [
        'Global' => [
            Data\Driver\DriverInterface::class => [
                'class' => Data\Driver\PDO\SQLite\DriverAdapter::class,
                'arguments' => [
                    'dsn'      => 'sqlite:'.realpath(__DIR__ . '/../../../build/webhemi_schema.sqlite3'),
                ],
                'shared' => true
            ],
            Data\Query\QueryInterface::class => [
                'class' => Data\Query\SQL\SqlQueryAdapter::class,
                'arguments' => [
                    Data\Driver\DriverInterface::class
                ]
            ]
        ],
    ],
];
