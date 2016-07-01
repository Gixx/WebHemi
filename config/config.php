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
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataEntity\User\UserMetaEntity;
use WebHemi\DataStorage\User\UserMetaStorage;
use WebHemi\DataStorage\User\UserStorage;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;

$localConfig = require __DIR__.'/local.php';

return [
    'dependencies' => [
        PDO::class => [
            'arguments' => [
                $localConfig['pdo']['dsn'],
                $localConfig['pdo']['username'],
                $localConfig['pdo']['password'],
                $localConfig['pdo']['options'],
            ],
            'calls'     => ['setAttribute' => [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]],
            'shared'    => true,
        ],
        HttpAdapterInterface::class => [
            'class'     => GuzzleHttpAdapter::class,
            'arguments' => [
                // This class requires arguments. The ApplicationInterface implementation must inject into it.
            ],
            'shared'    => true,
        ],
        RouterAdapterInterface::class => [
            'class'     => \WebHemi\Adapter\Router\FakeRouter::class, // TEST
            'shared'    => true,
        ],
        RendererAdapterInterface::class => [
            'class'     => \WebHemi\Adapter\Renderer\FakeRenderer::class, // TEST
            'shared'    => true,
        ],
        DataAdapterInterface::class => [
            'class'     => PDOAdapter::class,
            'arguments' => [PDO::class],
            'shared'    => true,
        ],
        RoutingMiddleware::class => [
            'arguments' => [RouterAdapterInterface::class]
        ],
        DispatcherMiddleware::class => [
            'arguments' => [
                RendererAdapterInterface::class,
                // This class requires additional argument. The ApplicationInterface implementation must inject into it.
            ]
        ],
        FinalMiddleware::class => [
            'arguments' => [
                RendererAdapterInterface::class,
                // This class requires additional argument. The ApplicationInterface implementation must inject into it.
            ]
        ],
        UserStorage::class => [
            'arguments' => [DataAdapterInterface::class, UserEntity::class],
            'shared'    => true,
        ],
        UserMetaStorage::class => [
            'arguments' => [DataAdapterInterface::class, UserMetaEntity::class],
            'shared'    => true,
        ],
        // Classes without any aliases, arguments or sharing options are optional to present here.
        UserEntity::class     => [],
        UserMetaEntity::class => [],
    ],
    'middleware_pipeline' => [
        ['class' => \WebHemi\Middleware\Action\FakeAction::class], // TEST
    ],
    'modules' => [
        'Admin' => [
            'application' => [
                // The default type is "subdir". "Subdomain" only when vhost supports it.
                'type'  => 'subdir',
                'path'  => 'admin',
                'theme' => 'default', // Allows JS and CSS customization only for the login page.
            ],
            'template_map' => [
                // For Admin it is fixed since it cannot be changed by themes.
            ],
            'routing' => [

            ],
        ],
        'Website' => [
            'application' => [
                // The only supported type for this application is "subdomain".
                'type'  => 'subdomain',
                'path'  => 'www',
                'theme' => 'default',
            ],
            'template_map' => [
                'error500' => ''
            ],
            'routing' => [
                'name'            => 'index',
                'path'            => '/',
                'middleware'      => '',
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
    'applications' => [
        // Custom applications using the "Website" module.
    ],
];
