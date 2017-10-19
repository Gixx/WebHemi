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
use WebHemi\Middleware\Action;
use WebHemi\Renderer;

return [
    'dependencies' => [
        'Website' => [
            Action\Website\UserAction::class => [
                'arguments' => [
                    Data\Storage\User\UserStorage::class,
                    Data\Storage\User\UserMetaStorage::class
                ]
            ],
            // Renderer Helpers
            Renderer\Helper\GetTagsHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class
                ]
            ],
            Renderer\Helper\GetCategoriesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class
                ]
            ],
            Renderer\Helper\GetDatesHelper::class => [
                'arguments' => [
                    Environment\ServiceInterface::class
                ]
            ]
        ],
    ],
];
