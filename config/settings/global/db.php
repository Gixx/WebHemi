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
use WebHemi\Data;

return [
    'dependencies' => [
        'Global' => [
            Data\ConnectorInterface::class => [
                'class'     => Data\Connector\PDO\SQLite\ConnectorAdapter::class,
                'arguments' => [
                    'name' => 'Test-SQLite',
                    Data\DriverInterface::class
                ],
            ],
            Data\DriverInterface::class => [
                'class' => Data\Connector\PDO\SQLite\DriverAdapter::class,
                'arguments' => [
                    'dsn'      => 'sqlite:'.realpath(__DIR__ . '/../../../build/webhemi_schema.sqlite3'),
                ],
                'shared' => true
            ],
            Data\MultiConnectorContainerInterface::class => [
                'class' => Data\Connector\MultiConnectorContainer::class,
                'arguments' => [
                    Data\ConnectorInterface::class
                ],
                'shared' => true
            ],
        ],
    ],
];
