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
use WebHemi\DependencyInjection;
use WebHemi\Environment;
use WebHemi\Http;
use WebHemi\I18n;
use WebHemi\Logger;
use WebHemi\Middleware;
use WebHemi\MiddlewarePipeline;
use WebHemi\Parser;
use WebHemi\Renderer;
use WebHemi\Router;
use WebHemi\Session;
use WebHemi\Validator;

return [
    'dependencies' => [
        'Global' => [
            // Core objects
            Configuration\ServiceInterface::class => [
                'class' => Configuration\ServiceAdapter\Base\ServiceAdapter::class,
            ],
            DependencyInjection\ServiceInterface::class => [
                'class' => DependencyInjection\ServiceAdapter\Base\ServiceAdapter::class,
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
            Application\ServiceInterface::class => [
                'class' => Application\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    DependencyInjection\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            // Services
            Acl\ServiceInterface::class => [
                'class'     => Acl\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    Environment\ServiceInterface::class,
                    Data\Storage\UserStorage::class,
                    Data\Storage\PolicyStorage::class,
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
                    Data\Storage\UserStorage::class,
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
            CSRF\ServiceInterface::class => [
                'class'     => CSRF\ServiceAdapter\Base\ServiceAdapter::class,
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
            I18n\ServiceInterface::class => [
                'class'     => I18n\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            I18n\DriverInterface::class => [
                'class'     => I18n\DriverAdapter\Gettext\DriverAdapter::class,
                'arguments' => [
                    I18n\ServiceInterface::class
                ],
            ],
            Parser\ServiceInterface::class => [
                'class'     => Parser\ServiceAdapter\Parsedown\ServiceAdapter::class,
                'shared'    => true,
            ],
            Renderer\ServiceInterface::class => [
                'class'     => Renderer\ServiceAdapter\Twig\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    I18n\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            Router\ServiceInterface::class => [
                'class'     => Router\ServiceAdapter\Base\ServiceAdapter::class,
                'arguments' => [
                    Configuration\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    Router\Result\Result::class
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
                'arguments' => []
            ],
            Middleware\Common\FinalMiddleware::class => [
                'arguments' => [
                    Auth\ServiceInterface::class,
                    Environment\ServiceInterface::class,
                    'AccessLog'
                ]
            ],
            // DataStorage
            Data\Storage\ApplicationStorage::class => [
                'arguments' => [
                    Data\Query\QueryInterface::class,
                    Data\Entity\EntitySet::class,
                    Data\Entity\ApplicationEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\UserStorage::class => [
                'arguments' => [
                    Data\Query\QueryInterface::class,
                    Data\Entity\EntitySet::class,
                    Data\Entity\UserEntity::class,
                    Data\Entity\UserGroupEntity::class,
                    Data\Entity\UserMetaEntity::class
                ],
                'shared'    => true,
            ],
            Data\Storage\PolicyStorage::class => [
                'arguments' => [
                    Data\Query\QueryInterface::class,
                    Data\Entity\EntitySet::class,
                    Data\Entity\PolicyEntity::class,
                ],
                'shared'    => true,
            ],
            Data\Storage\ResourceStorage::class => [
                'arguments' => [
                    Data\Query\QueryInterface::class,
                    Data\Entity\EntitySet::class,
                    Data\Entity\ResourceEntity::class,
                ],
                'shared'    => true,
            ],
            Data\Storage\FilesystemStorage::class => [
                'arguments' => [
                    Data\Query\QueryInterface::class,
                    Data\Entity\EntitySet::class,
                    Data\Entity\FilesystemCategoryEntity::class,
                    Data\Entity\FilesystemDirectoryEntity::class,
                    Data\Entity\FilesystemDirectoryDataEntity::class,
                    Data\Entity\FilesystemDocumentEntity::class,
                    Data\Entity\FilesystemEntity::class,
                    Data\Entity\FilesystemMetaEntity::class,
                    Data\Entity\FilesystemPublishedDocumentEntity::class,
                    Data\Entity\FilesystemTagEntity::class,
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
            Renderer\Filter\TranslateFilter::class => [
                'arguments' => [
                    I18n\DriverInterface::class
                ],
                'shared'    => true,
            ],
            Renderer\Filter\Tags\Url::class => [
                'arguments' => [
                    Environment\ServiceInterface::class
                ],
                'shared'    => true,
            ],
            Renderer\Filter\TagParserFilter::class => [
                'arguments' => [
                    Renderer\Filter\Tags\Url::class
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
            Renderer\Helper\FileExistsHelper::class => [
                'arguments' => [
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
                    Data\Storage\ResourceStorage::class,
                    Data\Storage\ApplicationStorage::class
                ],
                'shared'    => true,
            ],
            // Validators
            Validator\ValidatorCollection::class => [
                'arguments' => [
                    Validator\NotEmptyValidator::class,
                    Validator\RangeValidator::class
                ]
            ]
        ],
    ],
];
