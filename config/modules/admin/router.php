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
                'resource_name'   => 'admin-dashboard'
            ],

            // Content-Editor
            'admin-content-editor-index' => [
                'path'            => '^/content-editor/?$',
                'middleware'      => Action\Admin\ContentEditor\IndexAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel
            'admin-control-panel-index' => [
                'path'            => '^/control-panel/?$',
                'middleware'      => Action\Admin\ControlPanel\IndexAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Domains
            'admin-control-panel-domains-list' => [
                'path'            => '^/control-panel/domains/?$',
                'middleware'      => Action\Admin\ControlPanel\Domains\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Applications
            'admin-control-panel-applications-list' => [
                'path'            => '^/control-panel/applications/?$',
                'middleware'      => Action\Admin\ControlPanel\Applications\ListAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-applications-view' => [
                'path'            => '^/control-panel/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Applications\ViewAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-applications-add' => [
                'path'            => '^/control-panel/applications/?$',
                'middleware'      => Action\Admin\ControlPanel\Applications\AddAction::class,
                'allowed_methods' => ['POST']
            ],
            'admin-control-panel-applications-edit' => [
                'path'            => '^/control-panel/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Applications\EditAction::class,
                'allowed_methods' => ['PUT']
            ],
            'admin-control-panel-applications-delete' => [
                'path'            => '^/control-panel/applications/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Applications\DeleteAction::class,
                'allowed_methods' => ['DELETE']
            ],

            // Control Panel : Settings
            'admin-control-panel-settings-index' => [
                'path'            => '^/control-panel/settings/?$',
                'middleware'      => Action\Admin\ControlPanel\Settings\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Themes
            'admin-control-panel-themes-list' => [
                'path'            => '^/control-panel/themes/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\ListAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-themes-view' => [
                'path'            => '^/control-panel/themes/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\ViewAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-themes-add' => [
                'path'            => '^/control-panel/themes/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\AddAction::class,
                'allowed_methods' => ['POST']
            ],
            'admin-control-panel-themes-delete' => [
                'path'            => '^/control-panel/themes/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Themes\DeleteAction::class,
                'allowed_methods' => ['DELETE']
            ],

            // Control Panel : Add-Ons
            'admin-control-panel-add-ons-list' => [
                'path'            => '^/control-panel/add-ons/?$',
                'middleware'      => Action\Admin\ControlPanel\AddOns\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Users
            'admin-control-panel-users-list' => [
                'path'            => '^/control-panel/users/?$',
                'middleware'      => Action\Admin\ControlPanel\Users\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Groups
            'admin-control-panel-groups-list' => [
                'path'            => '^/control-panel/groups/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\ListAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-groups-add' => [
                'path'            => '^/control-panel/groups/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\AddAction::class,
                'allowed_methods' => ['POST']
            ],
            'admin-control-panel-groups-view' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\ViewAction::class,
                'allowed_methods' => ['GET']
            ],
            'admin-control-panel-groups-edit' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\EditAction::class,
                'allowed_methods' => ['PUT']
            ],
            'admin-control-panel-groups-delete' => [
                'path'            => '^/control-panel/groups/(?P<name>[a-z0-9\-\_]+)/?$',
                'middleware'      => Action\Admin\ControlPanel\Groups\DeleteAction::class,
                'allowed_methods' => ['DELETE']
            ],

            // Control Panel : Resources
            'admin-control-panel-resources-list' => [
                'path'            => '^/control-panel/resources/?$',
                'middleware'      => Action\Admin\ControlPanel\Resources\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Policies
            'admin-control-panel-policies-list' => [
                'path'            => '^/control-panel/policies/?$',
                'middleware'      => Action\Admin\ControlPanel\Policies\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // Control Panel : Logs
            'admin-control-panel-logs-list' => [
                'path'            => '^/control-panel/logs/?$',
                'middleware'      => Action\Admin\ControlPanel\Logs\ListAction::class,
                'allowed_methods' => ['GET']
            ],

            // About
            'admin-about-index' => [
                'path'            => '^/about/?$',
                'middleware'      => Action\Admin\About\IndexAction::class,
                'allowed_methods' => ['GET']
            ],
        ],
    ],
];
