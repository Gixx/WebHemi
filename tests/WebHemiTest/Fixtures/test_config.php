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

use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Config\Config;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemi\Routing\Result;
use WebHemiTest\Fixtures\TestAdapter;
use WebHemiTest\Fixtures\TestMiddleware;
use WebHemiTest\Fixtures\TestActionMiddleware;

$themeConfig = [
    'default' => [
        'map' => [
            'test-page' => 'unit/test.twig'
        ]
    ]
];

return [
    'applications' => [
        'Website' => [
            'path' => '/',
        ],
    ],
    'modules' => [
        'Website' => [
            'routing' => [
                'index' => [
                    'path' => '/',
                    'middleware' => 'actionOk',
                    'allowed_methods' => ['GET'],
                ],
                'error' => [
                    'path' => '/error/',
                    'middleware' => 'actionBad',
                    'allowed_methods' => ['GET'],
                ]
            ]
        ]
    ],
    'themes' => $themeConfig,
    'dependencies' => [
        'actionOk' => [
            'class' => TestActionMiddleware::class,
            'arguments' => [false],
        ],
        'actionBad' => [
            'class' => TestActionMiddleware::class,
            'arguments' => [true],
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
            'shared'    => true,
        ],
        RouterAdapterInterface::class => [
            'class'     => FastRouteAdapter::class,
            'arguments' => [Result::class],
            'shared'    => true,
        ],
        RendererAdapterInterface::class => [
            'class'     => TwigRendererAdapter::class,
            'arguments' => [
                new Config($themeConfig['default']),
                '/tests/WebHemiTest/Fixtures/test_theme',
                '/'
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
    'middleware_pipeline' => [
        ['class' => 'pipe1', 'priority' => 66],
        ['class' => 'pipe2', 'priority' => -20],
        ['class' => 'pipe3'],
        ['class' => 'pipe4', 'priority' => 120],
    ]
];
