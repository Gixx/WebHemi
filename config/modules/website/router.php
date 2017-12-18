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
                'path' => '^/$',
                'middleware' => Action\Website\IndexAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'website-view' => [
                'path' => '^(?P<path>\/[\w\/\-]*\w)?\/(?P<basename>(?!index\.html$)[\w\-\.]+\.[a-z0-9]{2,5})$',
                'middleware' => 'proxy',
                'allowed_methods' => ['GET'],
            ],
            'website-list' => [
                'path' => '^(?P<path>\/[\w\/\-]*\w)?\/(?P<basename>(?!index\.html$)[\w\-\.]+)(?:\/|\/index\.html)?$',
                'middleware' => 'proxy',
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
