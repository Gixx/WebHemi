<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Action\Website;

use InvalidArgumentException;
use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Data\Storage\StorageInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Router\ProxyInterface;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * @var EnvironmentInterface
     */
    protected $environmentManager;

    /**
     * @var StorageInterface[]
     */
    private $dataStorages;

    /**
     * IndexAction constructor.
     *
     * @param EnvironmentInterface $environmentManager
     * @param StorageInterface[]   ...$dataStorages
     */
    public function __construct(EnvironmentInterface $environmentManager, StorageInterface ...$dataStorages)
    {
        $this->environmentManager = $environmentManager;

        foreach ($dataStorages as $storage) {
            $this->dataStorages[get_class($storage)] = $storage;
        }
    }

    /**
     * Returns a stored storage instance.
     *
     * @param string $storageClass
     * @return StorageInterface
     */
    private function getStorage(string $storageClass) : StorageInterface
    {
        if (!isset($this->dataStorages[$storageClass])) {
            throw new InvalidArgumentException(
                sprintf('Storage class reference "%s" is not defined in this class.', $storageClass),
                1000
            );
        }

        return $this->dataStorages[$storageClass];
    }

    /**
     * Gets the application storage instance.
     *
     * @return Storage\ApplicationStorage
     */
    protected function getApplicationStorage() : Storage\ApplicationStorage
    {
        /** @var Storage\ApplicationStorage $storage */
        $storage = $this->getStorage(Storage\ApplicationStorage::class);

        return $storage;
    }

    /**
     * Gets the filesystem storage instance.
     *
     * @return Storage\FilesystemStorage
     */
    protected function getFilesystemStorage() : Storage\FilesystemStorage
    {
        /** @var Storage\FilesystemStorage $storage */
        $storage = $this->getStorage(Storage\FilesystemStorage::class);

        return $storage;
    }

    /**
     * Gets the user storage instance.
     *
     * @return Storage\UserStorage
     */
    protected function getUserStorage() : Storage\UserStorage
    {
        /** @var Storage\UserStorage $storage */
        $storage = $this->getStorage(Storage\UserStorage::class);

        return $storage;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $blogPosts = [];

        /**
         * @var Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /**
         * @var Entity\EntitySet $publications
         */
        $publications = $this->getFilesystemStorage()
            ->getFilesystemPublishedDocumentList($applicationEntity->getApplicationId());

        /**
         * @var Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
         */
        foreach ($publications as $publishedDocumentEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $publishedDocumentEntity);
        }

        return [
            'activeMenu' => '',
            'application' => $this->getApplicationData($applicationEntity),
            'fixPost' => $applicationEntity->getIntroduction(),
            'blogPosts' => $blogPosts,
        ];
    }

    /**
     * Gets application data to render.
     *
     * @param  Entity\ApplicationEntity $applicationEntity
     * @return array
     */
    protected function getApplicationData(Entity\ApplicationEntity $applicationEntity) : array
    {
        return [
            'name' => $applicationEntity->getName(),
            'title' => $applicationEntity->getTitle(),
            'introduction' => $applicationEntity->getIntroduction(),
            'subject' => $applicationEntity->getSubject(),
            'description' => $applicationEntity->getDescription(),
            'keywords' => $applicationEntity->getKeywords(),
            'coptright' => $applicationEntity->getCopyright()
        ];
    }

    /**
     * Collets the blog post data
     *
     * @param  Entity\ApplicationEntity                 $applicationEntity
     * @param  Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
     * @return array
     */
    protected function getBlobPostData(
        Entity\ApplicationEntity $applicationEntity,
        Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
    ) : array {
        /**
         * @var array $documentMeta
         */
        $documentMeta = $this->getFilesystemStorage()
            ->getSimpleFilesystemMetaListByFilesystem($publishedDocumentEntity->getFilesystemId());

        return [
            'author' => $this->getPublicationAuthor(
                $applicationEntity->getApplicationId(),
                $publishedDocumentEntity->getAuthorId()
            ),
            'tags' => $this->getPublicationTags(
                $applicationEntity->getApplicationId(),
                $publishedDocumentEntity->getFilesystemId()
            ),
            'category' => $this->getPublicationCategory(
                $applicationEntity->getApplicationId(),
                $publishedDocumentEntity->getCategoryId()
            ),
            'path' => $publishedDocumentEntity->getUri(),
            'title' => $publishedDocumentEntity->getTitle(),
            'summary' => $publishedDocumentEntity->getDescription(),
            'contentLead' => $publishedDocumentEntity->getContentLead(),
            'contentBody' => $publishedDocumentEntity->getContentBody(),
            'publishedAt' => $publishedDocumentEntity->getDatePublished(),
            'meta' => $documentMeta,
        ];
    }

    /**
     * Gets author information for a filesystem record.
     *
     * @param  int $applicationId
     * @param  int $userId
     * @return array
     */
    protected function getPublicationAuthor(int $applicationId, int $userId) : array
    {
        /**
         * @var Entity\UserEntity $user
         */
        $user = $this->getUserStorage()
            ->getUserById($userId);

        /**
         * @var array $userMeta
         */
        $userMeta = $this->getUserStorage()
            ->getSimpleUserMetaListByUser($userId);

        /**
         * @var Entity\FilesystemDirectoryDataEntity $userDirectoryData
         */
        $userDirectoryData = $this->getFilesystemStorage()
            ->getFilesystemDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_USER);

        return [
            'userId' => $userId,
            'userName' => $user->getUserName(),
            'url' => $userDirectoryData->getUri().'/'.$user->getUserName(),
            'meta' => $userMeta,
        ];
    }

    /**
     * Collects all the tags for a filesystem record.
     *
     * @param  int $applicationId
     * @param  int $filesystemId
     * @return array
     */
    protected function getPublicationTags(int $applicationId, int $filesystemId) : array
    {
        $tags = [];
        /**
         * @var Entity\EntitySet $tagEntities
         */
        $tagEntities = $this->getFilesystemStorage()
            ->getFilesystemTagListByFilesystem($filesystemId);

        /**
         * @var Entity\FilesystemDirectoryDataEntity $categoryDirectoryData
         */
        $categoryDirectoryData = $this->getFilesystemStorage()
            ->getFilesystemDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_TAG);

        /**
         * @var Entity\FilesystemTagEntity $tagEntity
         */
        foreach ($tagEntities as $tagEntity) {
            $tags[] = [
                'url' => $categoryDirectoryData->getUri().'/'.$tagEntity->getName(),
                'name' => $tagEntity->getName(),
                'title' => $tagEntity->getTitle()
            ];
        }

        return $tags;
    }

    /**
     * Gets the category for a filesystem record.
     *
     * @param  int $applicationId
     * @param  int $categoryId
     * @return array
     */
    protected function getPublicationCategory(int $applicationId, int $categoryId) : array
    {
        /**
         * @var Entity\FilesystemCategoryEntity $categoryEntity
         */
        $categoryEntity = $this->getFilesystemStorage()
            ->getFilesystemCategoryById($categoryId);

        /**
         * @var Entity\FilesystemDirectoryDataEntity $categoryDirectoryData
         */
        $categoryDirectoryData = $this->getFilesystemStorage()
            ->getFilesystemDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_CATEGORY);

        $category = [
            'url' => $categoryDirectoryData->getUri().'/'.$categoryEntity->getName(),
            'name' => $categoryEntity->getName(),
            'title' => $categoryEntity->getTitle()
        ];
        return $category;
    }
}
