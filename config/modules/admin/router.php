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
        'Admin' => [
            'index' => [
                'path'            => '/',
                'middleware'      => Action\Admin\DashboardAction::class,
                'allowed_methods' => ['GET'],
            ],
            'applications-index' => [
                'path'            => '/applications[/]',
                'middleware'      => Action\Admin\Applications\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            'applications-view' => [
                'path'            => '/applications/view/{name:[a-z0-9\-\_]+}[/]',
                'middleware'      => Action\Admin\Applications\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'applications-edit' => [
                'path'            => '/applications/edit/{name:[a-z0-9\-\_]+}[/]',
                'middleware'      => Action\Admin\Applications\EditAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'applications-add' => [
                'path'            => '/applications/add',
                'middleware'      => Action\Admin\Applications\AddAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'login' => [
                'path'            => '/auth/login[/]',
                'middleware'      => Action\Auth\LoginAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'logout' => [
                'path'            => '/auth/logout[/]',
                'middleware'      => Action\Auth\LogoutAction::class,
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
