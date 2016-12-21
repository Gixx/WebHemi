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

use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\DataDriverInterface;
use WebHemi\Adapter\Data\PDO\MySQLAdapter;
use WebHemi\Adapter\Log\Klogger\KloggerAdapter;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Adapter\Router\FastRoute\FastRouteAdapter;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Application\SessionManager;
use WebHemi\Auth\Auth as AuthAdapter;
use WebHemi\Auth\Result as AuthResult;
use WebHemi\Auth\AuthStorageInterface;
use WebHemi\Auth\Storage\Session;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\AccessManagement\PolicyStorage;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Form\Element\FormElementContainerInterface;
use WebHemi\Form\Element\Web\FormElementContainer as WebFormElementContainer;
use WebHemi\Form\Element\Web as WebFormElement;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemi\Routing\Result as RouteResult;

return [
    'dependencies' => [
        'Global' => [
            DataDriverInterface::class => [],
            // Adapter
            AuthAdapterInterface::class => [
                'class'     => AuthAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    AuthResult::class,
                    AuthStorageInterface::class,
                    UserStorage::class,
                ],
                'shared'    => true,
            ],
            HttpAdapterInterface::class => [
                'class'     => GuzzleHttpAdapter::class,
                'arguments' => [
                    EnvironmentManager::class,
                ],
                'shared'    => true,
            ],
            RouterAdapterInterface::class => [
                'class'     => FastRouteAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class,
                    RouteResult::class,
                ],
                'shared'    => true,
            ],
            RendererAdapterInterface::class => [
                'class'     => TwigRendererAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    EnvironmentManager::class
                ],
                'shared'    => true,
            ],
            DataAdapterInterface::class => [
                'class'     => MySQLAdapter::class,
                'arguments' => [
                    DataDriverInterface::class
                ],
                'shared'    => true,
            ],
            'AccessLog' => [
                'class'     => KloggerAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    '!:access'
                ]
            ],
            'EventLog' => [
                'class'     => KloggerAdapter::class,
                'arguments' => [
                    ConfigInterface::class,
                    '!:event'
                ]
            ],
            // Middleware
            RoutingMiddleware::class => [
                'arguments' => [
                    RouterAdapterInterface::class,
                ]
            ],
            DispatcherMiddleware::class => [
                'arguments' => [
                    RendererAdapterInterface::class,
                ]
            ],
            FinalMiddleware::class => [
                'arguments' => [
                    RendererAdapterInterface::class,
                    AuthAdapterInterface::class,
                    EnvironmentManager::class,
                    'AccessLog'
                ]
            ],
            // AuthStorage
            AuthStorageInterface::class => [
                'class'     => Session::class,
                'arguments' => [
                    SessionManager::class
                ],
                'shared'    => true,
            ],
            // DataStorage
            ApplicationStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    ApplicationEntity::class
                ],
                'shared'    => true,
            ],
            UserStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserEntity::class
                ],
                'shared'    => true,
            ],
            UserMetaStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserMetaEntity::class
                ],
                'shared'    => true,
            ],
            UserGroupStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            PolicyStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    PolicyEntity::class
                ],
                'shared'    => true,
            ],
            ResourceStorage::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    ResourceEntity::class
                ],
                'shared'    => true,
            ],
            // Data Couplers
            UserToPolicyCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserEntity::class,
                    PolicyEntity::class
                ],
                'shared'    => true,
            ],
            UserToGroupCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    UserEntity::class,
                    UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            UserGroupToPolicyCoupler::class => [
                'arguments' => [
                    DataAdapterInterface::class,
                    PolicyEntity::class,
                    UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            // Form
            FormElementContainerInterface::class => [
                'class' => WebFormElementContainer::class,
                'arguments' => [
                    WebFormElement\ButtonElement::class,
                    WebFormElement\CheckboxElement::class,
                    WebFormElement\DataListElement::class,
                    WebFormElement\FieldSetElement::class,
                    WebFormElement\FileElement::class,
                    WebFormElement\FormElement::class,
                    WebFormElement\HiddenElement::class,
                    WebFormElement\InputElement::class,
                    WebFormElement\KeygenElement::class,
                    WebFormElement\OutputElement::class,
                    WebFormElement\PasswordElement::class,
                    WebFormElement\RadioElement::class,
                    WebFormElement\ResetElement::class,
                    WebFormElement\SelectElement::class,
                    WebFormElement\StaticContentElement::class,
                    WebFormElement\SubmitElement::class,
                    WebFormElement\TextareaElement::class,
                    WebFormElement\TextElement::class
                ],
                'shared'    => true,
            ],
            // Classes without any aliases, arguments or sharing options are optional to present here.
            UserEntity::class     => [],
            UserMetaEntity::class => [],
        ],
    ],
];
