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
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Middleware\Action;
use WebHemi\Middleware\Security\AclMiddleware;

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
                    'allowed_methods' => ['GET'],
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
                    AuthAdapterInterface::class
                ]
            ],
            AclMiddleware::class => [
                'arguments' => [
                    AuthAdapterInterface::class,
                    UserToPolicyCoupler::class,
                    UserToGroupCoupler::class,
                    UserGroupToPolicyCoupler::class,
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
            ['service' => AclMiddleware::class, 'priority' => 10],
        ],
    ],
];
