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

use WebHemi\Adapter\Acl\AclAdapterInterface;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Auth\AuthCredentialInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Middleware\Action;
use WebHemi\Middleware\Security\AclMiddleware;
use WebHemi\Middleware\Security\AccessLogMiddleware;

return [
    'dependencies' => [
        'Admin' => [
            Action\Auth\LoginAction::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    AuthCredentialInterface::class,
                    EnvironmentManager::class,
                ],
            ],
            Action\Auth\LogoutAction::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    EnvironmentManager::class,
                ]
            ],
            AclMiddleware::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    AclAdapterInterface::class,
                    EnvironmentManager::class,
                    ApplicationStorage::class,
                    ResourceStorage::class,
                    UserMetaStorage::class
                ]
            ],
            AccessLogMiddleware::class => [
                'arguments' => [
                    'AccessLog',
                    AuthAdapterInterface::class,
                    EnvironmentManager::class
                ]
            ],
            Action\Admin\DashboardAction::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    EnvironmentManager::class
                ],
            ],
            Action\Admin\Applications\IndexAction::class => [
                'arguments' => [
                    ConfigInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class,
                    ApplicationStorage::class
                ],
            ],
            Action\Admin\Applications\ViewAction::class => [
                'arguments' => [
                    ConfigInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class
                ],
            ],
            Action\Admin\Applications\EditAction::class => [
                'arguments' => [
                    ConfigInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class
                ],
            ],
            Action\Admin\Applications\AddAction::class => [
                'arguments' => [
                    ConfigInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class
                ],
            ]
        ]
    ],
];
