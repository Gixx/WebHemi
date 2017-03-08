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

use WebHemi\Renderer\ServiceInterface as RendererAdapterInterface;
use WebHemi\Renderer\ServiceAdapter\Twig\ServiceAdapter as TwigRendererAdapter;
use WebHemi\Router\ServiceInterface as RouterAdapterInterface;
use WebHemi\Router\ServiceAdapter\FastRoute\ServiceAdapter as FastRouteAdapter;
use WebHemi\Http\ServiceInterface as HttpAdapterInterface;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\ServiceAdapter as GuzzleHttpAdapter;
use WebHemi\Environment\ServiceInterface as EnvironmentManager;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemi\Middleware\Common\FinalMiddleware;
use WebHemi\Middleware\Common\DispatcherMiddleware;
use WebHemi\Middleware\Common\RoutingMiddleware;
use WebHemi\Router\Result\Result;
use WebHemiTest\TestService\TestMiddleware;
use WebHemiTest\TestService\TestActionMiddleware;
use WebHemiTest\TestService\EmptyRendererHelper;

return [
    'applications' => [
        'website' => [
            'module' => 'Website',
            'type'   => 'domain',
            'path'   => 'www',
            'theme'  => 'default'
        ],
        'admin' => [
            'module' => 'Admin',
            'type'   => 'domain',
            'path'   => 'admin',
            'theme'  => 'test_theme'
        ],
        'some_app' => [
            'module' => 'SomeApp',
            'type'   => 'directory',
            'path'   => 'some_application',
            'theme'  => 'test_theme'
        ],
    ],
    'auth' => [],
    'dependencies' => [
        'Global' => [
            'actionOk' => [
                'class' => TestActionMiddleware::class,
                'arguments' => [false],
            ],
            'actionBad' => [
                'class' => TestActionMiddleware::class,
                'arguments' => [true],
            ],
            'actionForbidden' => [
                'class' => TestActionMiddleware::class,
                'arguments' => [true, 403],
            ],
            'pipe1' => [
                'class' => TestMiddleware::class,
                'arguments' => ['!:pipe1']
            ],
            'pipe2' => [
                'class' => TestMiddleware::class,
                'arguments' => ['!:pipe2']
            ],
            'pipe3' => [
                'class' => TestMiddleware::class,
                'arguments' => ['!:pipe3']
            ],
            'pipe4' => [
                'class' => TestMiddleware::class,
                'arguments' => ['!:pipe4']
            ],
            // Adapter
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
                    Result::class,
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
                'class' => TestMiddleware::class,
                'arguments' => ['!:final']
            ],
        ],
        'Website' => [],
        'SomeApp' => [],
    ],
    'logger' => [
        'unit' => [
            'path' => __DIR__.'/../../data/log',
            'file_name' => 'testlog-',
            'file_extension' => 'log',
            'date_format' => 'Y-m-d H:i:s.u',
            'log_level' => 0
        ],
    ],
    'middleware_pipeline' => [
        'Global' => [
            ['service' => 'pipe1', 'priority' => 66],
            ['service' => 'pipe2', 'priority' => -20],
            ['service' => 'pipe3'],
            ['service' => 'pipe4', 'priority' => 100],
            ['service' => FinalMiddleware::class],
        ],
        'Website' => [],
        'SomeApp' => [
            ['service' => 'someModuleAlias', 'priority' => 55],
        ],
    ],
    'renderer' => [
        'Global' => [
            'filter' => [

            ],
            'helper' => [
                EmptyRendererHelper::class
            ],
        ]
    ],
    'router' => [
        'Website' => [
            'index' => [
                'path'            => '/',
                'middleware'      => 'ActionOK',
                'allowed_methods' => ['GET','POST'],
            ],
            'login' => [
                'path'            => '/login',
                'middleware'      => 'SomeLoginMiddleware',
                'allowed_methods' => ['GET'],
            ],
            'error' => [
                'path' => '/error/',
                'middleware' => 'actionBad',
                'allowed_methods' => ['GET'],
            ],
            'forbidden' => [
                'path' => '/restricted/',
                'middleware' => 'actionForbidden',
                'allowed_methods' => ['GET'],
            ]
        ],
        'SomeApp' => [
            'index' => [
                'path'            => '/',
                'middleware'      => 'SomeIndexMiddleware',
                'allowed_methods' => ['GET','POST'],
            ],
            'somepath' => [
                'path'            => '/some/path',
                'middleware'      => 'SomeOtherMiddleware',
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
    'session' => [
        'namespace' => 'TEST',
        'cookie_prefix' => 'abcd',
        'session_name_salt' => 'WebHemiTestX'
    ],
    'themes' => [
        'default' => [
            'features' => [
                'admin_support' => true,
                'admin_login_support' => true,
                'website_support' => true
            ],
            'map' => [
                'test-page' => 'unit/test.twig',

            ],
        ],
        'test_theme' => [
            'features' => [
                'admin_support' => true,
                'admin_login_support' => true,
                'website_support' => true
            ],
            'map' => [
                'test-page' => 'unit/test.twig'
            ],
        ],
        'test_theme_no_admin' => [
            'features' => [
                'admin_support' => false,
                'admin_login_support' => true,
                'website_support' => true
            ],
            'map' => [
                'test-page' => 'unit/test.twig'
            ],
        ],
        'test_theme_no_admin_login' => [
            'features' => [
                'admin_support' => true,
                'admin_login_support' => false,
                'website_support' => true
            ],
            'map' => [
                'test-page' => 'unit/test.twig'
            ],
        ],
        'test_theme_no_website' => [
            'features' => [
                'admin_support' => true,
                'admin_login_support' => true,
                'website_support' => false
            ],
            'map' => [
                'test-page' => 'unit/test.twig'
            ],
        ],
    ],
];
