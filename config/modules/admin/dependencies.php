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

use WebHemi\Acl;
use WebHemi\Auth;
use WebHemi\Configuration;
use WebHemi\Data;
use WebHemi\Environment;
use WebHemi\Form;
use WebHemi\Middleware;

return [
    'dependencies' => [
        'Admin' => [
            // Pipeline elements
            Middleware\Security\AclMiddleware::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Acl\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\AccessManagement\ResourceStorage::class,
                    Data\Storage\User\UserMetaStorage::class
                ]
            ],
            Middleware\Security\AccessLogMiddleware::class => [
                'arguments' => [
                    'AccessLog',
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ]
            ],
            // Actions
            Middleware\Action\Auth\LoginAction::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Auth\CredentialInterface::class,
                    Environment\ServiceInterface::class,
                    Form\Preset\AdminLoginForm::class,
                ],
            ],
            Middleware\Action\Auth\LogoutAction::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                ]
            ],
            Middleware\Action\Admin\DashboardAction::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ],
            ],
            Middleware\Action\Admin\Applications\IndexAction::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class
                ],
            ],
            Middleware\Action\Admin\Applications\AddAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\IndexAction::class,
            ],
            Middleware\Action\Admin\Applications\ViewAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\AddAction::class,
            ],
            Middleware\Action\Admin\Applications\PreferencesAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\ViewAction::class,
            ],
            Middleware\Action\Admin\Applications\DeleteAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\AddAction::class,
            ],
            Middleware\Action\Admin\ControlPanel\Groups\ListAction::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\User\UserGroupStorage::class
                ]
            ],
            Middleware\Action\Admin\ControlPanel\Groups\ViewAction::class => [
                'inherits' => Middleware\Action\Admin\ControlPanel\Groups\ListAction::class
            ],
            Middleware\Action\Admin\ControlPanel\Themes\IndexAction::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                ],
            ],
            // Form Presets
            Form\Preset\AdminLoginForm::class => [
                'arguments' => [
                    Form\ServiceAdapter\Base\ServiceAdapter::class,
                    Form\Element\Html\HtmlElement::class
                ]
            ]
        ]
    ],
];
