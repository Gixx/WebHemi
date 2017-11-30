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

namespace WebHemi\Router\Proxy;

use WebHemi\Data\Storage;
use WebHemi\Data\Entity;
use WebHemi\Data\StorageInterface;
use WebHemi\Router\ProxyInterface;
use WebHemi\Router\Result\Result;

/**
 * Class FilesystemProxy
 */
class FilesystemProxy implements ProxyInterface
{
    /** @var StorageInterface[] */
    private $dataStorages = [];

    /**
     * FilesystemProxy constructor.
     *
     * @param StorageInterface[] ...$dataStorages
     */
    public function __construct(StorageInterface ...$dataStorages)
    {
        foreach ($dataStorages as $instance) {
            $storageClass = get_class($instance);

            $this->dataStorages[$storageClass] = $instance;
        }
    }

    /**
     * Resolves the middleware class name for the application and URL.
     *
     * @param string $application
     * @param Result $routeResult
     * @return void
     */
    public function resolveMiddleware(string $application, Result &$routeResult) : void
    {
        $applicationEntity = $this->getApplicationEntity($application);

        if (!$applicationEntity) {
            return;
        }

        $parameters = $routeResult->getParameters();
        $fileSystemEntity = $this->getFilesystemEntityByRouteParams($applicationEntity, $parameters);

        if (!$fileSystemEntity) {
            return;
        }

        if ($fileSystemEntity->getType() == Entity\Filesystem\FilesystemEntity::TYPE_DIRECTORY) {
            // DirectoryId must exists, as well as the relevant directory entity...
            $fileSystemDirectoryEntity = $this->getFilesystemDirectoryEntity($fileSystemEntity->getDirectoryId());
            // Theoretically this alway should be valid, since the proxy is not editable
            $middleware = $fileSystemDirectoryEntity->getProxy() ?? self::LIST_POST;

            $routeResult->setMatchedMiddleware($middleware);
        } else {
            $routeResult->setMatchedMiddleware(self::VIEW_POST);
        }

        $routeResult->setParameters($parameters);
    }

    /**
     * Gets the application entity.
     *
     * @param string $application
     * @return Entity\ApplicationEntity
     */
    private function getApplicationEntity(string $application) : ? Entity\ApplicationEntity
    {
        /** @var Storage\ApplicationStorage $applicationStorage */
        $applicationStorage = $this->dataStorages[Storage\ApplicationStorage::class] ?? null;

        if (!$applicationStorage) {
            return null;
        }

        return $applicationStorage->getApplicationByName($application);
    }

    /**
     * Gets the filesystem entity.
     *
     * @param Entity\ApplicationEntity $applicationEntity
     * @param array $parameters
     * @return null|Entity\Filesystem\FilesystemEntity
     */
    private function getFilesystemEntityByRouteParams(
        Entity\ApplicationEntity $applicationEntity,
        array &$parameters
    ) : ? Entity\Filesystem\FilesystemEntity {
        /** @var Storage\Filesystem\FilesystemStorage $fileSystemStorage */
        $fileSystemStorage = $this->dataStorages[Storage\Filesystem\FilesystemStorage::class] ?? null;

        if (!$fileSystemStorage) {
            return null;
        }

        $path = $parameters['path'];
        $baseName = $parameters['basename'];

        $fileSystemEntity = $fileSystemStorage->getFilesystemData(
            $applicationEntity->getApplicationId(),
            $path,
            $baseName
        );

        // If we don't find it as a created content, we try with the preserved contents
        if (!$fileSystemEntity && $path != '/') {
            $uri = trim($path.'/'.$baseName, '/');
            $parts = explode('/', $uri);

            $parameters = [
                'path' => '/',
                'basename' => array_shift($parts),
                'uri_parameter' => implode('/', $parts)
            ];

            $fileSystemEntity = $this->getFilesystemEntityByRouteParams($applicationEntity, $parameters);
        }

        return $fileSystemEntity;
    }

    /**
     * Gets the directory entity.
     *
     * @param int $identifier
     * @return null|Entity\Filesystem\FilesystemDirectoryEntity
     */
    private function getFilesystemDirectoryEntity(int $identifier) : ? Entity\Filesystem\FilesystemDirectoryEntity
    {
        /** @var Storage\Filesystem\FilesystemDirectoryStorage $fileSystemDirectoryStorage */
        $fileSystemDirectoryStorage = $this->dataStorages[Storage\Filesystem\FilesystemDirectoryStorage::class] ?? null;

        if (!$fileSystemDirectoryStorage) {
            return null;
        }

        return $fileSystemDirectoryStorage->getFilesystemDirectoryById($identifier);
    }
}
