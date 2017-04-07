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
            // Auth
            'admin-login' => [
                'path'            => '/auth/login[/]',
                'middleware'      => Action\Auth\LoginAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'admin-logout' => [
                'path'            => '/auth/logout[/]',
                'middleware'      => Action\Auth\LogoutAction::class,
                'allowed_methods' => ['GET'],
            ],
            // Dashboard
            'dashboard' => [
                'path'            => '/',
                'middleware'      => Action\Admin\DashboardAction::class,
                'allowed_methods' => ['GET'],
                'resource_name'   => 'admin-dashboard', // Define resorce name in the options
            ],
            // Applications
            'admin-applications-list' => [ // Define resource name in the index (fallback if no option presents)
                'path'            => '/applications[/]',
                'middleware'      => Action\Admin\Applications\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-add' => [
                'path'            => '/applications/add',
                'middleware'      => Action\Admin\Applications\AddAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-view' => [
                'path'            => '/applications/{name:[a-z0-9\-\_]+}[/]',
                'middleware'      => Action\Admin\Applications\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-preferences' => [
                'path'            => '/applications/{name:[a-z0-9\-\_]+}/preferences[/]',
                'middleware'      => Action\Admin\Applications\PreferencesAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-save' => [
                'path'            => '/applications/{name:[a-z0-9\-\_]+}/{action:save}[/]',
                'middleware'      => Action\Admin\Applications\SaveAction::class,
                'allowed_methods' => ['POST'],
            ],
            'admin-applications-delete' => [
                'path'            => '/applications/{name:[a-z0-9\-\_]+}/delete[/]',
                'middleware'      => Action\Admin\Applications\DeleteAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            // Control Panel
            'admin-control-panel-index' => [
                'path'            => '/control-panel[/]',
                'middleware'      => Action\Admin\ControlPanel\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            // Themes
            'admin-control-panel-themes-list' => [
                'path'            => '/control-panel/themes[/]',
                'middleware'      => Action\Admin\ControlPanel\Themes\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-themes-add' => [
                'path'            => '/control-panel/themes/add[/]',
                'middleware'      => Action\Admin\ControlPanel\Themes\AddAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],
            'admin-control-panel-themes-view' => [
                'path'            => '/control-panel/themes/{name:[a-z0-9\-\_]+}[/]',
                'middleware'      => Action\Admin\ControlPanel\Themes\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-themes-delete' => [
                'path'            => '/control-panel/themes/{name:[a-z0-9\-\_]+}/delete[/]',
                'middleware'      => Action\Admin\ControlPanel\Themes\DeleteAction::class,
                'allowed_methods' => ['GET', 'POST'],
            ],

            // About
            'admin-about-index' => [
                'path'            => '/about[/]',
                'middleware'      => Action\Admin\About\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
