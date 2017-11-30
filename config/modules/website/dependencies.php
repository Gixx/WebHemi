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
                    Router\ProxyInterface::class
                ],
                'shared'    => true,
            ],
            Router\ProxyInterface::class => [
                'class' => Router\Proxy\FilesystemProxy::class,
                'arguments' => [
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\Filesystem\FilesystemStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class
                ],
                'shared'    => true,
            ],
            // Proxies
            'view-post' => [
                'class' => Website\View\PostAction::class,
            ],
            'list-post' => [
                'class' => Website\Directory\PostAction::class,
            ],
            'list-category' => [
                'class' => Website\Directory\CategoryAction::class,
            ],
            'list-tag' => [
                'class' => Website\Directory\TagAction::class,
            ],
            'list-archive' => [
                'class' => Website\Directory\ArchiveAction::class,
            ],
            'list-gallery' => [
                'class' => Website\Directory\GalleryAction::class,
            ],
            'list-binary' => [
                'class' => Website\Directory\BinaryAction::class,
            ],
            'list-user' => [
                'class' => Website\Directory\UserAction::class,
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\User\UserStorage::class,
                    Data\Storage\User\UserMetaStorage::class,
                    Data\Storage\Filesystem\FilesystemStorage::class,
                    Data\Storage\Filesystem\FilesystemTagStorage::class,
                    Data\Storage\Filesystem\FilesystemCategoryStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class,
                    Data\Storage\Filesystem\FilesystemDocumentStorage::class,
                ]
            ],
            // Actions
            Website\IndexAction::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\User\UserStorage::class,
                    Data\Storage\User\UserMetaStorage::class,
                    Data\Storage\Filesystem\FilesystemStorage::class,
                    Data\Storage\Filesystem\FilesystemTagStorage::class,
                    Data\Storage\Filesystem\FilesystemCategoryStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class,
                    Data\Storage\Filesystem\FilesystemDocumentStorage::class,
                ]
            ],
            // Renderer Helpers
            Renderer\Helper\GetTagsHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\Filesystem\FilesystemTagStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class,
                ]
            ],
            Renderer\Helper\GetCategoriesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\Filesystem\FilesystemCategoryStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class,
                ]
            ],
            Renderer\Helper\GetDatesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\Filesystem\FilesystemStorage::class,
                    Data\Storage\Filesystem\FilesystemDirectoryStorage::class,
                ]
            ]
        ],
    ],
];
