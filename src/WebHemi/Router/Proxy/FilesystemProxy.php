<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Router\Proxy;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\FilesystemStorage;
use WebHemi\Router\ProxyInterface;
use WebHemi\Router\Result\Result;

/**
 * Class FilesystemProxy
 */
class FilesystemProxy implements ProxyInterface
{
    /**
     * @var ApplicationStorage
     */
    private $applicationStorage;

    /**
     * @var FilesystemStorage
     */
    private $filesystemStorage;

    /**
     * FilesystemProxy constructor.
     *
     * @param ApplicationStorage $applicationStorage
     * @param FilesystemStorage $filesystemStorage
     */
    public function __construct(
        ApplicationStorage $applicationStorage,
        FilesystemStorage $filesystemStorage
    ) {
        $this->applicationStorage = $applicationStorage;
        $this->filesystemStorage = $filesystemStorage;
    }

    /**
     * Resolves the middleware class name for the application and URL.
     *
     * @param  string $applicationName
     * @param  Result $routeResult
     * @return void
     */
    public function resolveMiddleware(string $applicationName, Result&$routeResult) : void
    {
        /**
         * @var null|Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->applicationStorage->getApplicationByName($applicationName);

        if (!$applicationEntity instanceof Entity\ApplicationEntity) {
            return;
        }

        $parameters = $routeResult->getParameters();

        /**
         * @var Entity\FilesystemEntity $fileSystemEntity
         */
        $fileSystemEntity = $this->getFilesystemEntityByRouteParams($applicationEntity, $routeResult);

        if (!$fileSystemEntity instanceof Entity\FilesystemEntity) {
            $routeResult->setStatus(Result::CODE_NOT_FOUND)
                ->setMatchedMiddleware(null);
            return;
        }

        if ($fileSystemEntity->getType() == Entity\FilesystemEntity::TYPE_DIRECTORY) {
            $this->validateDirectoryMiddleware($fileSystemEntity, $routeResult);
        } else {
            $routeResult->setStatus(Result::CODE_FOUND)
                ->setMatchedMiddleware(self::VIEW_POST);
        }

        $routeResult->setParameters($parameters);
    }

    /**
     * @param Entity\FilesystemEntity $fileSystemEntity
     * @param Result                  $routeResult
     * @return void
     */
    protected function validateDirectoryMiddleware(
        Entity\FilesystemEntity $fileSystemEntity,
        Result&$routeResult
    ) : void {
        // DirectoryId must exists, as well as the relevant directory entity...
        $fileSystemDirectoryEntity = $this->filesystemStorage
            ->getFilesystemDirectoryById((int) $fileSystemEntity->getFilesystemDirectoryId());

        if ($fileSystemDirectoryEntity instanceof Entity\FilesystemDirectoryEntity
            && $fileSystemDirectoryEntity->getIsAutoIndex() === false
        ) {
            $routeResult->setStatus(Result::CODE_FORBIDDEN)
                ->setMatchedMiddleware(null);
        } else {
            // Theoretically this alway should be valid, since the proxy is not editable
            $middleware = $fileSystemDirectoryEntity->getProxy() ?? self::LIST_POST;
            $routeResult->setStatus(Result::CODE_FOUND)
                ->setMatchedMiddleware($middleware);
        }
    }

    /**
     * Gets the filesystem entity.
     *
     * @param  Entity\ApplicationEntity $applicationEntity
     * @param  Result                   $routeResult
     * @return null|Entity\FilesystemEntity
     */
    private function getFilesystemEntityByRouteParams(
        Entity\ApplicationEntity $applicationEntity,
        Result&$routeResult
    ) : ? Entity\FilesystemEntity {
        $parameters = $routeResult->getParameters();
        $path = $parameters['path'];
        $baseName = $parameters['basename'];

        /**
         * @var null|Entity\FilesystemEntity $fileSystemEntity
         */
        $fileSystemEntity = $this->filesystemStorage->getFilesystemByApplicationAndPath(
            (int) $applicationEntity->getApplicationId(),
            $path,
            $baseName
        );

        // If we don't find it as a created content, we try with the preserved contents (tag, categories etc)
        if (!$fileSystemEntity && $path != '/' && $routeResult->getResource() == 'website-list') {
            $uri = trim($path.'/'.$baseName, '/');
            $parts = explode('/', $uri);

            $parameters = [
                'path' => '/',
                'basename' => array_shift($parts),
                'uri_parameter' => implode('/', $parts)
            ];

            $routeResult->setParameters($parameters);

            $fileSystemEntity = $this->getFilesystemEntityByRouteParams($applicationEntity, $routeResult);
        }

        return $fileSystemEntity;
    }
}
