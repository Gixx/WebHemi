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

namespace WebHemi\Middleware\Action\Traits;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Router\ProxyInterface;

/**
 * Trait GetPublicationTagsTrait
 *
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 * @method Storage\Filesystem\FilesystemTagStorage getFilesystemTagStorage()
 */
trait GetPublicationTagsTrait
{
    /**
     * Collects all the tags for a filesystem record.
     *
     * @param int $applicationId
     * @param int $filesystemId
     * @return array
     */
    protected function getPublicationTags(int $applicationId, int $filesystemId) : array
    {
        $tags = [];
        /** @var Entity\Filesystem\FilesystemTagEntity[] $tagEntities */
        $tagEntities = $this->getFilesystemTagStorage()
            ->getFilesystemTagsByFilesystem($filesystemId);

        if (!empty($tagEntities)) {
            /** @var array $categoryDirectoryData */
            $categoryDirectoryData = $this->getFilesystemDirectoryStorage()
                ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_TAG);

            /** @var Entity\Filesystem\FilesystemTagEntity $tagEntity */
            foreach ($tagEntities as $tagEntity) {
                $tags[] = [
                    'url' => $categoryDirectoryData['uri'].'/'.$tagEntity->getName(),
                    'name' => $tagEntity->getName(),
                    'title' => $tagEntity->getTitle()
                ];
            }
        }

        return $tags;
    }
}
