<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

use WebHemi\Acl;
use WebHemi\Application;
use WebHemi\Auth;
use WebHemi\Configuration;
use WebHemi\CSRF;
use WebHemi\Data;
use WebHemi\Environment;
use WebHemi\Form;
use WebHemi\Middleware;
use WebHemi\Session;
use WebHemi\Validator;

return [
    'dependencies' => [
        'Admin' => [
            Application\Progress::class => [
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Session\ServiceInterface::class
                ],
                'shared' => false
            ],
            // Pipeline elements
            Middleware\Security\AclMiddleware::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Acl\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    Data\Storage\ResourceStorage::class,
                    Data\Storage\UserStorage::class
                ]
            ],
            Middleware\Security\AccessLogMiddleware::class => [
                'arguments' => [
                    'AccessLog',
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ]
            ],
            Middleware\Security\CSRFMiddleware::class => [
                'arguments' => [
                    CSRF\ServiceInterface::class
                ]
            ],
            // Actions
            Middleware\Action\Auth\LoginAction::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Auth\CredentialInterface::class,
                    Environment\ServiceInterface::class,
                    'AdminLoginForm',
                    CSRF\ServiceInterface::class
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
                    Data\Storage\ApplicationStorage::class,
                    'ApplicationCreateForm',
                    CSRF\ServiceInterface::class
                ],
            ],
            Middleware\Action\Admin\Applications\ViewAction::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\ApplicationStorage::class,
                    'ApplicationEditForm',
                    CSRF\ServiceInterface::class
                ],
            ],
            Middleware\Action\Admin\Applications\AddAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\ViewAction::class,
            ],
            Middleware\Action\Admin\Applications\EditAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\ViewAction::class,
            ],
            Middleware\Action\Admin\Applications\DeleteAction::class => [
                'inherits' => Middleware\Action\Admin\Applications\ViewAction::class,
            ],
            Middleware\Action\Admin\ControlPanel\Groups\ListAction::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Data\Storage\UserStorage::class
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
            // Form Presets - looks kinda hack, but it is by purpose.
            Form\PresetInterface::class => [
                'class' => Form\Preset\SimplePreset::class,
                'arguments' => [
                    Form\ServiceAdapter\Base\ServiceAdapter::class,
                    Validator\ValidatorCollection::class,
                    Form\Element\Html\HtmlElement::class,
                ]
            ],
            'AdminLoginForm' => [
                'class'     => Form\Preset\AdminLoginForm::class,
                'inherits' => Form\PresetInterface::class
            ],
            'ApplicationCreateForm' => [
                'class'     => Form\Preset\ApplicationCreateForm::class,
                'inherits' => Form\PresetInterface::class
            ],
            'ApplicationEditForm' => [
                'class'     => Form\Preset\ApplicationEditForm::class,
                'inherits' => Form\PresetInterface::class
            ]
        ]
    ],
];
