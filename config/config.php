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
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\PDO\PDOAdapter;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Application\SessionManager;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemi\Routing\Result;

require_once __DIR__.'/functions.php';

$config = [
    'applications' => [],
    'themes' => [],
    'modules' => [],
    'middleware_pipeline' => [
// Can be different in applications, so define it in the application configs
//        ['service' => LockCheckMiddleware::class, 'priority' => -100],
//        ['service' => AuthMiddleware::class, 'priority' => -50],
//        ['service' => AclMiddleware::class, 'priority' => 33],
//        ['service' => CacheReaderMiddleware::class, 'priority' => 66],
//        ['service' => CacheWriterMiddleware::class, 'priority' => 150],
//        ['service' => 'SomeCustomServiceAlias', 'priority' => 200],
    ],
    'dependencies' => [
        'Global' => [
            // Library
            PDO::class => [
                'arguments' => get_pdo_config(),
                'calls'     => ['setAttribute' => [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]],
                'shared'    => true,
            ],
            // Session
            SessionManager::class => [
                'shared'    => true
            ],
            // Adapter
            HttpAdapterInterface::class => [
                'class'     => GuzzleHttpAdapter::class,
                'arguments' => [
                    // This class requires arguments.
                ],
                'shared'    => true,
            ],
            RouterAdapterInterface::class => [
                'class'     => FastRouteAdapter::class,
                'arguments' => [
                    Result::class,
                    // This class requires additional argument.
                ],
                'shared'    => true,
            ],
            RendererAdapterInterface::class => [
                'class'     => TwigRendererAdapter::class,
                'arguments' => [
                    // This class requires arguments.
                ],
                'shared'    => true,
            ],
            DataAdapterInterface::class => [
                'class'     => PDOAdapter::class,
                'arguments' => [
                    PDO::class
                ],
                'shared'    => true,
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
                ]
            ],
            // Storage
            UserStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserEntity::class
                ],
                'shared'    => true,
            ],
            UserMetaStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserMetaEntity::class
                ],
                'shared'    => true,
            ],
            UserGroupStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserGroupEntity::class
                ],
                'shared'    => true,
            ],

            // Classes without any aliases, arguments or sharing options are optional to present here.
            UserEntity::class     => [],
            UserMetaEntity::class => [],
        ],
    ],
];

return merge_array_overwrite($config, get_module_config(), get_application_config(), get_theme_config());
