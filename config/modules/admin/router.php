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

use WebHemi\Middleware\Action;

return [
    'router' => [
        'Admin' => [
            // Auth
            'admin-login' => [
                'path'            => '^/auth/login/?$',
                'middleware'      => Action\Auth\LoginAction::class,
                'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            ],
            'admin-logout' => [
                'path'            => '^/auth/logout/?$',
                'middleware'      => Action\Auth\LogoutAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Dashboard
            'dashboard' => [
                'path'            => '^/$',
                'middleware'      => Action\Admin\DashboardAction::class,
                'allowed_methods' => ['GET'],
                'resource_name'   => 'admin-dashboard', // Define resorce name in the options
            ],

            // Applications
            'admin-applications-list' => [ // Define resource name in the index (fallback if no option presents)
                'path'            => '^/applications/?$',
                'middleware'      => Action\Admin\Applications\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-add' => [
                'path'            => '^/applications/?$',
                'middleware'      => Action\Admin\Applications\AddAction::class,
                'allowed_methods' => ['POST'],
            ],
            'admin-applications-view' => [
                'path'            => '^/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\Applications\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-applications-edit' => [
                'path'            => '^/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\Applications\EditAction::class,
                'allowed_methods' => ['PUT'],
            ],
            'admin-applications-delete' => [
                'path'            => '^/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\Applications\DeleteAction::class,
                'allowed_methods' => ['DELETE'],
            ],

            // Control Panel
            'admin-control-panel-index' => [
                'path'            => '^/control-panel/?$',
                'middleware'      => Action\Admin\ControlPanel\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Settings
            'admin-control-panel-settings-index' => [
                'path'            => '^/control-panel/settings/?$',
                'middleware'      => Action\Admin\ControlPanel\Settings\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Themes
            'admin-control-panel-themes-list' => [
                'path'            => '^/control-panel/themes/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-themes-add' => [
                'path'            => '^/control-panel/themes/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\AddAction::class,
                'allowed_methods' => ['POST'],
            ],
            'admin-control-panel-themes-view' => [
                'path'            => '^/control-panel/themes/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-themes-delete' => [
                'path'            => '^/control-panel/themes/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\DeleteAction::class,
                'allowed_methods' => ['DELETE'],
            ],

            // Add-Ons
            'admin-control-panel-add-ons-index' => [
                'path'            => '^/control-panel/add-ons/?$',
                'middleware'      => Action\Admin\ControlPanel\AddOns\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Users
            'admin-control-panel-users-list' => [
                'path'            => '^/control-panel/users/?$',
                'middleware'      => Action\Admin\ControlPanel\Users\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Groups
            'admin-control-panel-groups-list' => [
                'path'            => '^/control-panel/groups/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\ListAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-groups-add' => [
                'path'            => '^/control-panel/groups/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\AddAction::class,
                'allowed_methods' => ['POST'],
            ],
            'admin-control-panel-groups-view' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\ViewAction::class,
                'allowed_methods' => ['GET'],
            ],
            'admin-control-panel-groups-edit' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\EditAction::class,
                'allowed_methods' => ['PUT'],
            ],
            'admin-control-panel-groups-delete' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\DeleteAction::class,
                'allowed_methods' => ['DELETE'],
            ],

            // Resources
            'admin-control-panel-resources-list' => [
                'path'            => '^/control-panel/resources/?$',
                'middleware'      => Action\Admin\ControlPanel\Resources\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Policies
            'admin-control-panel-policies-list' => [
                'path'            => '^/control-panel/policies/?$',
                'middleware'      => Action\Admin\ControlPanel\Policies\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // Logs
            'admin-control-panel-logs-list' => [
                'path'            => '^/control-panel/logs/?$',
                'middleware'      => Action\Admin\ControlPanel\Logs\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],

            // About
            'admin-about-index' => [
                'path'            => '^/about/?$',
                'middleware'      => Action\Admin\About\IndexAction::class,
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
