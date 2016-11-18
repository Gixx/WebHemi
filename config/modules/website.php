<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Form\Element\FormElementContainerInterface;
use WebHemi\Form\Web as WebForm;
use WebHemi\Application\SessionManager;

return [
    'modules' => [
        'Website' => [
            'routing' => [
                'index' => [
                    'path'            => '/',
                    'middleware'      => \WebHemi\Middleware\Action\FakeAction::class,
                    'allowed_methods' => ['GET', 'POST'],
                ],
                'view' => [
                    'path'            => '/view/{id:.*}',
                    'middleware'      => \WebHemi\Middleware\Action\FakeViewAction::class,
                    'allowed_methods' => ['GET'],
                ],
            ],
        ],
    ],
    'dependencies' => [
        'Website' => [
            \WebHemi\Middleware\Action\FakeAction::class => [
                'arguments' => [
                    UserStorage::class,
                    WebForm\TestForm::class,
                    SessionManager::class,
                    UserToPolicyCoupler::class,
                    UserToGroupCoupler::class,
                    UserGroupToPolicyCoupler::class,
                ],
            ],
            WebForm\TestForm::class => [
                'arguments' => [
                    FormElementContainerInterface::class
                ]
            ],
            \WebHemi\Middleware\Action\FakeViewAction::class => [
                'arguments' => [
                    UserStorage::class
                ],
            ],
        ],
    ],
    'middleware_pipeline' => [
        'Website' => [
        ],
    ],
];
