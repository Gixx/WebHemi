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
            'website-post-list' => [
                'path'            => '/posts',
                'middleware'      => Action\Website\PostListAction::class,
                'allowed_methods' => ['GET'],
            ],
            'website-post-view' => [
                'path'            => '/posts/view/{id:.*}',
                'middleware'      => Action\Website\PostViewAction::class,
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
