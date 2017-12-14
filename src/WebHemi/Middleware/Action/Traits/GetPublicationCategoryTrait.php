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
 * Trait GetPublicationCategoryTrait
 *
 * @method Storage\Filesystem\FilesystemCategoryStorage getFilesystemCategoryStorage()
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 */
trait GetPublicationCategoryTrait
{
    /**
     * Gets the category for a filesystem record.
     *
     * @param int $applicationId
     * @param int $categoryId
     * @return array
     */
    protected function getPublicationCategory(int $applicationId, int $categoryId) : array
    {
        /** @var Entity\Filesystem\FilesystemCategoryEntity $categoryEntity */
        $categoryEntity = $this->getFilesystemCategoryStorage()
            ->getFilesystemCategoryById($categoryId);

        /** @var array $categoryDirectoryData */
        $categoryDirectoryData = $this->getFilesystemDirectoryStorage()
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_CATEGORY);

        $category = [
            'url' => $categoryDirectoryData['uri'].'/'.$categoryEntity->getName(),
            'name' => $categoryEntity->getName(),
            'title' => $categoryEntity->getTitle()
        ];
        return $category;
    }
}
