<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Log\LogAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Middleware\Action;
use WebHemi\Middleware\Security\AclMiddleware;
use WebHemi\Middleware\Security\AccessLogMiddleware;

return [
    'modules' => [
        'Admin' => [
            'routing' => [
                'index' => [
                    'path'            => '/',
                    'middleware'      => Action\Admin\DashboardAction::class,
                    'allowed_methods' => ['GET'],
                ],
                'login' => [
                    'path'            => '/auth/login',
                    'middleware'      => Action\Auth\LoginAction::class,
                    'allowed_methods' => ['GET', 'POST'],
                ],
                'logout' => [
                    'path'            => '/auth/logout',
                    'middleware'      => Action\Auth\LogoutAction::class,
                    'allowed_methods' => ['GET'],
                ],
            ],
        ],
    ],
    'dependencies' => [
        'Admin' => [
            Action\Auth\LoginAction::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    EnvironmentManager::class,
                    UserStorage::class,
                    UserGroupStorage::class,
                    UserToGroupCoupler::class,
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
                    EnvironmentManager::class,
                    UserToPolicyCoupler::class,
                    UserToGroupCoupler::class,
                    UserGroupToPolicyCoupler::class,
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
                ],
            ],
        ]
    ],
    'middleware_pipeline' => [
        'Admin' => [
            ['service' => AccessLogMiddleware::class, 'priority' => 9],
            ['service' => AclMiddleware::class, 'priority' => 10],
        ],
    ],
];
