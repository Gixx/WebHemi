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
use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataEntity\User\UserMetaEntity;
use WebHemi\DataStorage\User\UserMetaStorage;
use WebHemi\DataStorage\User\UserStorage;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemi\Routing\Result;

require_once __DIR__.'/functions.php';

return [
    'applications' => get_application_config(),
    'themes' => get_theme_config(),
    'middleware_pipeline' => [
//        ['class' => LockCheckMiddleware::class, 'priority' => -10],
//        ['class' => AuthMiddleware::class, 'priority' => -5],
//        ['class' => AclMiddleware::class, 'priority' => 10],
//        ['class' => CacheReaderMiddleware::class, 'priority' => 20],
//        ['class' => CacheWriterMiddleware::class, 'priority' => 110],
    ],
    'dependencies' => [
        // Library
        PDO::class => [
            'arguments' => get_database_config(),
            'calls'     => ['setAttribute' => [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]],
            'shared'    => true,
        ],
        // Adapter
        HttpAdapterInterface::class => [
            'class'     => GuzzleHttpAdapter::class,
            'arguments' => [
                // This class requires arguments. The ApplicationInterface implementation must inject into it.
            ],
            'shared'    => true,
        ],
        RouterAdapterInterface::class => [
            'class'     => FastRouteAdapter::class,
            'arguments' => [
                Result::class,
                // This class requires additional argument. The ApplicationInterface implementation must inject into it.
            ],
            'shared'    => true,
        ],
        RendererAdapterInterface::class => [
            'class'     => TwigRendererAdapter::class,
            'arguments' => [
                // This class requires arguments. The ApplicationInterface implementation must inject into it.
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
        // Classes without any aliases, arguments or sharing options are optional to present here.
        UserEntity::class     => [],
        UserMetaEntity::class => [],
    ],
    'modules' => [
        'Admin' => [
            'routing' => [
                'index' => [
                    'path'            => '/',
                    'middleware'      => \WebHemi\Middleware\Action\FakeAction::class,
                    'allowed_methods' => ['POST'],
                ]
            ],
        ],
        'Website' => [
            'routing' => [
                'index' => [
                    'path'            => '/',
                    'middleware'      => \WebHemi\Middleware\Action\FakeAction::class,
                    'allowed_methods' => ['GET'],
                ]
            ],
        ],
    ],
];
