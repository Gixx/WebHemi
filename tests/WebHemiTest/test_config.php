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

use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemi\DateTime;
use WebHemi\Data;
use WebHemi\Environment\ServiceInterface as EnvironmentManager;
use WebHemi\Http\ServiceInterface as HttpAdapterInterface;
use WebHemi\Http\ServiceAdapter\GuzzleHttp\ServiceAdapter as GuzzleHttpAdapter;
use WebHemi\I18n\ServiceInterface as I18nServiceAdapterInterface;
use WebHemi\I18n\ServiceAdapter\Base\ServiceAdapter as I18nServiceAdapter;
use WebHemi\I18n\DriverInterface as I18nDriverAdapterInterface;
use WebHemi\I18n\DriverAdapter\Gettext\DriverAdapter as I18nDriverAdapter;
use WebHemi\Middleware\Common\FinalMiddleware;
use WebHemi\Middleware\Common\DispatcherMiddleware;
use WebHemi\Middleware\Common\RoutingMiddleware;
use WebHemi\Renderer\ServiceInterface as RendererAdapterInterface;
use WebHemi\Renderer\ServiceAdapter\Twig\ServiceAdapter as TwigRendererAdapter;
use WebHemi\Router\ServiceInterface as RouterAdapterInterface;
use WebHemi\Router\ProxyInterface as RouterProxyInterface;
use WebHemi\Router\ServiceAdapter\Base\ServiceAdapter as RouteAdapter;
use WebHemi\Router\Result\Result;
use WebHemiTest\TestService\EmptyRendererHelper;
use WebHemiTest\TestService\EmptyService;
use WebHemiTest\TestService\TestMiddleware;
use WebHemiTest\TestService\TestActionMiddleware;
use WebHemiTest\TestService\EmptyRouteProxy as RouteProxy;

return [
    'applications' => [
        'website' => [
            'module' => 'Website',
            'type'   => 'domain',
            'path'   => 'www',
            'theme'  => 'default',
            'language' => 'en',
            'locale' => 'en_GB.UTF-8',
            'timezone' => 'Europe/London',
        ],
        'admin' => [
            'module' => 'Admin',
            'type'   => 'domain',
            'path'   => 'admin',
            'theme'  => 'test_theme',
            'language' => 'en',
            'locale' => 'en_US.UTF-8',
            'timezone' => 'America/Detroit',
        ],
        'some_app' => [
            'module' => 'SomeApp',
            'type'   => 'directory',
            'path'   => 'some_application',
            'theme'  => 'test_theme',
            'language' => 'pt',
            'locale' => 'pt_BR.UTF-8',
            'timezone' => 'America/Sao_Paulo',
        ],
    ],
    'auth' => [],
    'dependencies' => [
        'Global' => [
            'actionOk' => [
                'class' => TestActionMiddleware::class,
                'arguments' => ['just a boolean' => false],
            ],
            'actionBad' => [
                'class' => TestActionMiddleware::class,
                'arguments' => ['boolean' => true],
            ],
            'actionForbidden' => [
                'class' => TestActionMiddleware::class,
                'arguments' => ['should simulate an error?' => true, 'response code' => 403],
            ],
            'pipe1' => [
                'class' => TestMiddleware::class,
                'arguments' => ['this is the name' => 'pipe1']
            ],
            'pipe2' => [
                'class' => TestMiddleware::class,
                'arguments' => ['literal' => 'pipe2']
            ],
            'pipe3' => [
                'class' => TestMiddleware::class,
                'arguments' => ['no reference lookup for this' => 'pipe3']
            ],
            'pipe4' => [
                'class' => TestMiddleware::class,
                'arguments' => ['pipe name' => 'pipe4']
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
                'class'     => RouteAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class,
                    Result::class,
                    RouterProxyInterface::class
                ],
                'shared'    => true,
            ],
            RouterProxyInterface::class => [
                'class' => RouteProxy::class,
                'arguments' => [],
                'shared'    => true,
            ],
            RendererAdapterInterface::class => [
                'class'     => TwigRendererAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class,
                    I18nServiceAdapterInterface::class
                ],
                'shared'    => true,
            ],
            I18nServiceAdapterInterface::class => [
                'class'     => I18nServiceAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class
                ],
                'shared'    => true,
            ],
            I18nDriverAdapterInterface::class => [
                'class'     => I18nDriverAdapter::class,
                'arguments' => [
                    I18nServiceAdapterInterface::class
                ],
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
                'arguments' => ['this is the final middleware simulation' => 'final']
            ],
        ],
        'Website' => [
            DateTime::class => [
                'arguments' => [
                    'time' => '2016-04-05 01:02:03',
                    DateTimeZone::class
                ]
            ],
            DateTimeZone::class => [
                'arguments' => ['timezone' => 'Europe/Berlin']
            ],
            'ThisWillHurt' => [
                'class' => WebHemiTest\TestService\TestExceptionMiddleware::class
            ],
        ],
        'SomeApp' => [
            'alias' => [
                'class' => ArrayObject::class,
                'arguments' => [
                    'input' => ['something', 2]
                ]
            ],
            'otherAlias' => [
                'inherits' => 'alias'
            ],
            'moreAlias' => [
                'inherits' => 'otherAlias',
                'shared' => true
            ],
            'lastInherit' => [
                'inherits' => 'moreAlias'
            ],
            EmptyService::class => [
                'inherits' => 'lastInherit',
                'shared' => false
            ],
            'alias1' => [
                'class' => DateTime::class,
                'arguments' => [
                    'date' => '2016-04-05 01:02:03'
                ]
            ],
            'special' => [
                'class'  => ArrayObject::class,
                'calls'  => [['offsetSet', ['offset_name' => 'date', 'alias1']]],
                'shared' => true
            ]
        ],
        'OtherApp' => [
            'aliasWithReference' => [
                'class' => EmptyService::class,
                'arguments' => [
                    'key' => 'theKey',
                    DateTime::class
                ]
            ],
            'aliasWithFalseReference' => [
                'class' => EmptyService::class,
                'arguments' => [
                    'key' => 'theKey',
                    'ItIsNotAClassName'
                ]
            ],
            'aliasWithLiteral' => [
                'class' => EmptyService::class,
                'arguments' => [
                    'key' => 'theKey',
                    'data' => DateTime::class
                ]
            ]
        ]
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
                'path'            => '^/$',
                'middleware'      => 'actionOk',
                'allowed_methods' => ['GET','POST'],
            ],
            'login' => [
                'path'            => '^/login$',
                'middleware'      => 'SomeLoginMiddleware',
                'allowed_methods' => ['GET'],
            ],
            'error' => [
                'path'            => '^/error/?$',
                'middleware'      => 'actionBad',
                'allowed_methods' => ['GET'],
            ],
            'forbidden' => [
                'path'            => '^/restricted/?$',
                'middleware'      => 'actionForbidden',
                'allowed_methods' => ['GET'],
            ],
            'proxy-view-test' => [
                'path' => '^/proxytest'
                    .'(?P<path>\/[\w\/\-]*\w)?\/(?P<basename>(?!index\.html$)[\w\-\.]+\.[a-z0-9]{2,5})$',
                'middleware'      => 'proxy',
                'allowed_methods' => ['GET'],
            ],
            'proxy-list-test' => [
                'path' => '^/proxytest'
                    .'(?P<path>\/[\w\/\-]*\w)?\/(?P<basename>(?!index\.html$)[\w\-\.]+)(?:\/|\/index\.html)?$',
                'middleware'      => 'proxy',
                'allowed_methods' => ['GET'],
            ],
        ],
        'SomeApp' => [
            'index' => [
                'path'            => '^/$',
                'middleware'      => 'SomeIndexMiddleware',
                'allowed_methods' => ['GET','POST'],
            ],
            'somepath' => [
                'path'            => '^/some/path$',
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
    'ftp' => [],
    'email' => []
];
