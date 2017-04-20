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
use WebHemi\Application;
use WebHemi\Auth;
use WebHemi\Configuration;
use WebHemi\Data;
use WebHemi\DependencyInjection;
use WebHemi\Environment;
use WebHemi\Http;
use WebHemi\Logger;
use WebHemi\Middleware;
use WebHemi\MiddlewarePipeline;
use WebHemi\Parser;
use WebHemi\Renderer;
use WebHemi\Router;
use WebHemi\Session;

return [
    'dependencies' => [
        'Global' => [
            // Core objects
            Application\ServiceInterface::class => [
                'class' => Application\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            Configuration\ServiceInterface::class => [
                'class' => Configuration\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            DependencyInjection\ServiceInterface::class => [
                'class' => DependencyInjection\ServiceAdapter\Symfony\ServiceAdapter::class,
            ],
            Environment\ServiceInterface::class => [
                'class' => Environment\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            MiddlewarePipeline\ServiceInterface::class => [
                'class' => MiddlewarePipeline\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            Session\ServiceInterface::class => [
                'class' => Session\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            // Services
            Acl\ServiceInterface::class => [
                'class'     => Acl\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Coupler\UserToPolicyCoupler::class,
                    Data\Coupler\UserToGroupCoupler::class,
                    Data\Coupler\UserGroupToPolicyCoupler::class,
                ],
                'shared'    => true,
            ],
            Auth\CredentialInterface::class => [
                'class'     => Auth\Credential\NameAndPasswordCredential::class,
                'shared'    => false,
            ],
            Auth\ResultInterface::class => [
                'class'     => Auth\Result\Result::class,
                'shared'    => false,
            ],
            Auth\ServiceInterface::class => [
                'class'     => Auth\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Auth\ResultInterface::class,
                    Auth\StorageInterface::class,
                    Data\Storage\User\UserStorage::class,
                ],
                'shared'    => true,
            ],
            Auth\StorageInterface::class => [
                'class'     => Auth\Storage\Session::class,
                'arguments' => [
                    Session\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            Http\ServiceInterface::class => [
                'class'     => Http\ServiceAdapter\GuzzleHttp\ServiceAdapter::class,
                'arguments' => [
                    Environment\ServiceInterface::class,
                ],
                'shared'    => true,
            ],
            Parser\ServiceInterface::class => [
                'class'     => Parser\ServiceAdapter\Parsedown\ServiceAdapter::class,
                'shared'    => true,
            ],
            Renderer\ServiceInterface::class => [
                'class'     => Renderer\ServiceAdapter\Twig\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            Router\ServiceInterface::class => [
                'class'     => Router\ServiceAdapter\FastRoute\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Router\Result\Result::class,
                ],
                'shared'    => true,
            ],
            // Logger
            'AccessLog' => [
                'class'     => Logger\ServiceAdapter\Klogger\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    'logType' => 'access'
                ]
            ],
            'EventLog' => [
                'class'     => Logger\ServiceAdapter\Klogger\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    'logType' => 'event'
                ]
            ],
            // Middleware
            Middleware\Common\RoutingMiddleware::class => [
                'arguments' => [
                    Router\ServiceInterface::class,
                ]
            ],
            Middleware\Common\DispatcherMiddleware::class => [
                'arguments' => [
                    Renderer\ServiceInterface::class,
                ]
            ],
            Middleware\Common\FinalMiddleware::class => [
                'arguments' => [
                    Renderer\ServiceInterface::class,
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    'AccessLog'
                ]
            ],
            // DataStorage
            Data\Storage\ApplicationStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\ApplicationEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\User\UserStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\User\UserEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\User\UserMetaStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\User\UserMetaEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\User\UserGroupStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\AccessManagement\PolicyStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\AccessManagement\PolicyEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\AccessManagement\ResourceStorage::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\AccessManagement\ResourceEntity::class
                ],
                'shared'    => true,
            ],
            // Data Couplers
            Data\Coupler\UserToPolicyCoupler::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\User\UserEntity::class,
                    Data\Entity\AccessManagement\PolicyEntity::class
                ],
                'shared'    => true,
            ],
            Data\Coupler\UserToGroupCoupler::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\User\UserEntity::class,
                    Data\Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            Data\Coupler\UserGroupToPolicyCoupler::class => [
                'arguments' => [
                    Data\ConnectorInterface::class,
                    Data\Entity\AccessManagement\PolicyEntity::class,
                    Data\Entity\User\UserGroupEntity::class
                ],
                'shared'    => true,
            ],
            // Renderer Filter
            Renderer\Filter\MarkDownFilter::class => [
                'arguments' => [
                    Parser\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            // Renderer Helper
            Renderer\Helper\DefinedHelper::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            Renderer\Helper\GetStatHelper::class => [
                'shared'    => true,
            ],
            Renderer\Helper\IsAllowedHelper::class => [
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Acl\ServiceInterface::class,
                    Auth\ServiceInterface::class,
                    Data\Storage\AccessManagement\ResourceStorage::class,
                    Data\Storage\ApplicationStorage::class
                ],
                'shared'    => true,
            ]
        ],
    ],
];
