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
use WebHemi\Middleware\Action;

return [
    'router' => [
        'Website' => [
            'website-index' => [
                'path'            => '/',
                'middleware'      => Action\Website\IndexAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'website-post-view' => [
                'path'            => '{path:.+}.html',
                'middleware'      => Action\Website\PostViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'website-category-list' => [
                'path'            => '/category/{category:.+}',
                'middleware'      => Action\Website\PostListAction::class,
                'allowed_methods' => ['GET'],
            ],
            'website-tag-list' => [
                'path'            => '/tag/{tag:.+}',
                'middleware'      => Action\Website\PostListAction::class,
                'allowed_methods' => ['GET'],
            ],
            'website-date-list' => [
                'path'            => '/archive/{date:\d\d\d\d\-\d\d}',
                'middleware'      => Action\Website\PostListAction::class,
                'allowed_methods' => ['GET'],
            ],
            'website-user-page' => [
                'path'            => '/user/{username:.+}',
                'middleware'      => Action\Website\UserAction::class,
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
