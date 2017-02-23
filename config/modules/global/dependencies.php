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

use WebHemi\Acl\Acl as AclAdapter;
use WebHemi\Adapter\Acl\AclAdapterInterface;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Auth\AuthCredentialInterface;
use WebHemi\Adapter\Auth\AuthResultInterface;
use WebHemi\Adapter\Auth\AuthStorageInterface;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\DataDriverInterface;
use WebHemi\Adapter\Data\PDO\MySQLAdapter;
use WebHemi\Adapter\Log\Klogger\KloggerAdapter;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Application\SessionManager;
use WebHemi\Auth\Auth as AuthAdapter;
use WebHemi\Auth\Result as AuthResult;
use WebHemi\Auth\Credential\NameAndPasswordCredential as AuthCredential;
use WebHemi\Auth\Storage\Session as AuthStorage;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Entity;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Storage;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemi\Renderer\Filter;
use WebHemi\Renderer\Helper;
use WebHemi\Router\Result as RouteResult;

return [
    'dependencies' => [
        'Global' => [
            DataDriverInterface::class => [],
            // Adapter
            AuthAdapterInterface::class => [
                'class'     => AuthAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    AuthResultInterface::class,
                    AuthStorageInterface::class,
                    Storage\User\UserStorage::class,
                ],
                'shared'    => true,
            ],
            AuthCredentialInterface::class => [
                'class'     => AuthCredential::class,
                'shared'    => true,
            ],
            AuthResultInterface::class => [
                'class'     => AuthResult::class,
                'shared'    => true,
            ],
            AuthStorageInterface::class => [
                'class'     => AuthStorage::class,
                'arguments' => [
                    SessionManager::class
                ],
                'shared'    => true,
            ],
            AclAdapterInterface::class => [
                'class'     => AclAdapter::class,
                'arguments' => [
                    UserToPolicyCoupler::class,
                    UserToGroupCoupler::class,
                    UserGroupToPolicyCoupler::class,
                ],
                'shared'    => true,
            ],
            HttpAdapterInterface::class => [
                'class'     => GuzzleHttpAdapter::class,
                'arguments' => [
                    EnvironmentManager::class,
                ],
                'shared'    => true,
            ],
            RouterAdapterInterface::class => [
                'class'     => FastRouteAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class,
                    RouteResult::class,
                ],
                'shared'    => true,
            ],
            RendererAdapterInterface::class => [
                'class'     => TwigRendererAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class
                ],
                'shared'    => true,
            ],
            DataAdapterInterface::class => [
                'class'     => MySQLAdapter::class,
                'arguments' => [
                    DataDriverInterface::class
                ],
                'shared'    => true,
            ],
            'AccessLog' => [
                'class'     => KloggerAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    '!:access'
                ]
            ],
            'EventLog' => [
                'class'     => KloggerAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    '!:event'
                ]
            ],
            // Middleware
            RoutingMiddleware::class => [
                'arguments' => [
                    RouterAdapterInterface::class,
                ]
            ],
            DispatcherMiddleware::class => [
                'arguments' => [
                    RendererAdapterInterface::class,
                ]
            ],
            FinalMiddleware::class => [
                'arguments' => [
                    RendererAdapterInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class,
                    'AccessLog'
                ]
            ],
            // DataStorage
            Storage\ApplicationStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\ApplicationEntity::class
                ],
                'shared'    => true,
            ],
            Storage\User\UserStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\User\UserEntity::class
                ],
                'shared'    => true,
            ],
            Storage\User\UserMetaStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\User\UserMetaEntity::class
                ],
                'shared'    => true,
            ],
            Storage\User\UserGroupStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            Storage\AccessManagement\PolicyStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\AccessManagement\PolicyEntity::class
                ],
                'shared'    => true,
            ],
            Storage\AccessManagement\ResourceStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\AccessManagement\ResourceEntity::class
                ],
                'shared'    => true,
            ],
            // Data Couplers
            UserToPolicyCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\User\UserEntity::class,
                    Entity\AccessManagement\PolicyEntity::class
                ],
                'shared'    => true,
            ],
            UserToGroupCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\User\UserEntity::class,
                    Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            UserGroupToPolicyCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    Entity\AccessManagement\PolicyEntity::class,
                    Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            // Renderer Helper
            Helper\DefinedHelper::class => [
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class
                ],
                'shared'    => true,
            ],
            Helper\GetStatHelper::class => [
                'shared'    => true,
            ],
            Helper\IsAllowedHelper::class => [
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class,
                    AclAdapterInterface::class,
                    AuthAdapterInterface::class,
                    Storage\AccessManagement\ResourceStorage::class,
                    Storage\ApplicationStorage::class
                ],
                'shared'    => true,
            ]
        ],
    ],
];
