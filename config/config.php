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
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\PDO\PDOAdapter;
use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataEntity\User\UserMetaEntity;
use WebHemi\DataStorage\User\UserMetaStorage;
use WebHemi\DataStorage\User\UserStorage;

$localConfig = require __DIR__.'/local.php';

return [
    'dependencies' => [
        PDO::class => [
            'arguments' => [
                $localConfig['pdo']['dsn'],
                $localConfig['pdo']['username'],
                $localConfig['pdo']['password'],
                $localConfig['pdo']['options'],
            ],
            'calls'     => ['setAttribute' => [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION]],
            'shared'    => true,
        ],
        DataAdapterInterface::class => [
            'class'     => PDOAdapter::class,
            'arguments' => [PDO::class],
            'shared'    => true,
        ],
        UserStorage::class => [
            'arguments' => [DataAdapterInterface::class, UserEntity::class],
        ],
        UserMetaStorage::class => [
            'arguments' => [DataAdapterInterface::class, UserMetaEntity::class],
        ],
        UserEntity::class     => [],
        UserMetaEntity::class => [],
    ],
    'modules' => [
        'Admin' => [
            'application' => [
                // The default type is "subdir". "Subdomain" only when vhost supports it.
                'type'  => 'subdir',
                'path'  => 'admin',
                'theme' => 'default',
            ],
            'template_map' => [

            ],
            'routing' => [

            ],
        ],
        'Website' => [
            'application' => [
                // The only supported type for this application is "subdomain".
                'type'  => 'subdomain',
                'path'  => 'www',
                'theme' => 'default',
            ],
            'template_map' => [

            ],
            'routing' => [
                'name'            => 'index',
                'path'            => '/',
                'middleware'      => '',
                'allowed_methods' => ['GET'],
            ],
        ],
    ],
];
