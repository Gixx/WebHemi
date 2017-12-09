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
declare(strict_types = 1);

namespace WebHemi\Data\Traits;

use WebHemi\Data\StorageInterface;
use WebHemi\Data\Storage;

/**
 * Trait StorageTrait
 */
trait StorageInjectorTrait
{
    /** @var StorageInterface[] */
    protected $dataStorages = [];

    /**
     * Add Storage instances to internal library.
     *
     * @param StorageInterface[] ...$dataStorages
     */
    protected function addStorageInstances(array $dataStorages) : void
    {
        foreach ($dataStorages as $instance) {
            if ($instance instanceof StorageInterface) {
                $storageClass = get_class($instance);

                $this->dataStorages[$storageClass] = $instance;
            }
        }
    }

    /**
     * @return null|Storage\ApplicationStorage
     */
    protected function getApplicationStorage() : ? Storage\ApplicationStorage
    {
        return $this->dataStorages[Storage\ApplicationStorage::class] ?? null;
    }

    /**
     * @return null|Storage\Filesystem\FilesystemStorage
     */
    protected function getFilesystemStorage() : ? Storage\Filesystem\FilesystemStorage
    {
        return $this->dataStorages[Storage\Filesystem\FilesystemStorage::class] ?? null;
    }

    /**
     * @return null|Storage\Filesystem\FilesystemCategoryStorage
     */
    protected function getFilesystemCategoryStorage() : ? Storage\Filesystem\FilesystemCategoryStorage
    {
        return $this->dataStorages[Storage\Filesystem\FilesystemCategoryStorage::class] ?? null;
    }

    /**
     * @return null|Storage\Filesystem\FilesystemDirectoryStorage
     */
    protected function getFilesystemDirectoryStorage() : ? Storage\Filesystem\FilesystemDirectoryStorage
    {
        return $this->dataStorages[Storage\Filesystem\FilesystemDirectoryStorage::class] ?? null;
    }

    /**
     * @return null|Storage\Filesystem\FilesystemDocumentStorage
     */
    protected function getFilesystemDocumentStorage() : ? Storage\Filesystem\FilesystemDocumentStorage
    {
        return $this->dataStorages[Storage\Filesystem\FilesystemDocumentStorage::class] ?? null;
    }

    /**
     * @return null|Storage\Filesystem\FilesystemTagStorage
     */
    protected function getFilesystemTagStorage() : ? Storage\Filesystem\FilesystemTagStorage
    {
        return $this->dataStorages[Storage\Filesystem\FilesystemTagStorage::class] ?? null;
    }

    /**
     * @return null|Storage\User\UserStorage
     */
    protected function getUserStorage() : ? Storage\User\UserStorage
    {
        return $this->dataStorages[Storage\User\UserStorage::class] ?? null;
    }

    /**
     * @return null|Storage\User\UserMetaStorage
     */
    protected function getUserMetaStorage() : ? Storage\User\UserMetaStorage
    {
        return $this->dataStorages[Storage\User\UserMetaStorage::class] ?? null;
    }
}
