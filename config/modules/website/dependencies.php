<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
use WebHemi\Data;
use WebHemi\Environment;
use WebHemi\Middleware\Action\Website;
use WebHemi\Renderer;
use WebHemi\Router;

return [
    'dependencies' => [
        'Website' => [
            // Services
            Router\ServiceInterface::class => [
                'class'     => Router\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    // This will be added to the Global definition
                    3 => Router\ProxyInterface::class
                ],
                'shared'    => true,
            ],
            Router\ProxyInterface::class => [
                'class' => Router\Proxy\FilesystemProxy::class,
                'arguments' => [
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\FilesystemStorage::class
                ],
                'shared'    => true,
            ],
            // Actions
            Website\IndexAction::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\UserStorage::class,
                    Data\Storage\FilesystemStorage::class,
                ]
            ],
            // Proxies
            'view-post' => [
                'class' => Website\View\PostAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-post' => [
                'class' => Website\Directory\PostAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-category' => [
                'class' => Website\Directory\CategoryAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-tag' => [
                'class' => Website\Directory\TagAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-archive' => [
                'class' => Website\Directory\ArchiveAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-gallery' => [
                'class' => Website\Directory\GalleryAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-binary' => [
                'class' => Website\Directory\BinaryAction::class,
                'inherits' => Website\IndexAction::class
            ],
            'list-user' => [
                'class' => Website\Directory\UserAction::class,
                'inherits' => Website\IndexAction::class
            ],
            // Renderer Helpers
            Renderer\Helper\GetTagsHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\FilesystemStorage::class,
                ]
            ],
            Renderer\Helper\GetCategoriesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\FilesystemStorage::class,
                ]
            ],
            Renderer\Helper\GetDatesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\FilesystemStorage::class,
                ]
            ]
        ],
    ],
];
