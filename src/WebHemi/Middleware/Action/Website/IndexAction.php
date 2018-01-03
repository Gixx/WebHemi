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
declare(strict_types = 1);

namespace WebHemi\Middleware\Action\Website;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Data\StorageInterface;
use WebHemi\Data\Traits\StorageInjectorTrait;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Router\ProxyInterface;

/**
 * Class IndexAction.
 *
 * @method Storage\ApplicationStorage getApplicationStorage()
 * @method Storage\Filesystem\FilesystemCategoryStorage getFilesystemCategoryStorage()
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 * @method Storage\Filesystem\FilesystemDocumentStorage getFilesystemDocumentStorage()
 * @method Storage\Filesystem\FilesystemStorage getFilesystemStorage()
 * @method Storage\Filesystem\FilesystemTagStorage getFilesystemTagStorage()
 * @method Storage\User\UserStorage getUserStorage()
 * @method Storage\User\UserMetaStorage getUserMetaStorage()
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var EnvironmentInterface */
    protected $environmentManager;

    use StorageInjectorTrait;

    /**
     * IndexAction constructor.
     *
     * @param EnvironmentInterface $environmentManager
     * @param StorageInterface[] ...$dataStorages
     */
    public function __construct(EnvironmentInterface $environmentManager, StorageInterface ...$dataStorages)
    {
        $this->environmentManager = $environmentManager;
        $this->addStorageInstances($dataStorages);
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

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getPublishedDocuments($applicationEntity->getApplicationId());

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
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
     * @param Entity\ApplicationEntity $applicationEntity
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
     * @param Entity\ApplicationEntity $applicationEntity
     * @param Entity\Filesystem\FilesystemEntity $filesystemEntity
     * @return array
     */
    protected function getBlobPostData(
        Entity\ApplicationEntity $applicationEntity,
        Entity\Filesystem\FilesystemEntity $filesystemEntity
    ) : array {
        /** @var Entity\Filesystem\FilesystemDocumentEntity $documentEntity */
        $documentEntity = $this->getFilesystemDocumentStorage()
            ->getFilesystemDocumentById($filesystemEntity->getDocumentId());

        $documentMeta = $this->getFilesystemStorage()
            ->getPublicationMeta($filesystemEntity->getFilesystemId());

        $author = $this->getPublicationAuthor(
            $documentEntity->getAuthorId(),
            $applicationEntity->getApplicationId()
        );

        return [
            'author' => $author,
            'tags' => $this->getPublicationTags(
                $applicationEntity->getApplicationId(),
                $filesystemEntity->getFilesystemId()
            ),
            'category' => $this->getPublicationCategory(
                $applicationEntity->getApplicationId(),
                $filesystemEntity->getCategoryId()
            ),
            'path' => $this->getPublicationPath($filesystemEntity),
            'title' => $filesystemEntity->getTitle(),
            'summary' => $filesystemEntity->getDescription(),
            'contentLead' => $documentEntity->getContentLead(),
            'contentBody' => $documentEntity->getContentBody(),
            'publishedAt' => $filesystemEntity->getDatePublished(),
            'meta' => $documentMeta,
        ];
    }

    /**
     * Gets author information for a filesystem record.
     *
     * @param int $userId
     * @param int $applicationId
     * @return array
     */
    protected function getPublicationAuthor(int $userId, int $applicationId) : array
    {
        /** @var Entity\User\UserEntity $user */
        $user = $this->getUserStorage()
            ->getUserById($userId);

        /** @var array $userMeta */
        $userMeta = $this->getUserMetaStorage()
            ->getUserMetaArrayForUserId($userId);

        /** @var array $userDirectoryData */
        $userDirectoryData = $this->getFilesystemDirectoryStorage()
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_USER);

        return [
            'userId' => $userId,
            'userName' => $user->getUserName(),
            'url' => $userDirectoryData['uri'].'/'.$user->getUserName(),
            'meta' => $userMeta,
        ];
    }

    /**
     * Generates the content path.
     *
     * @param Entity\Filesystem\FilesystemEntity $filesystemEntity
     * @return string
     */
    protected function getPublicationPath(Entity\Filesystem\FilesystemEntity $filesystemEntity) : string
    {
        $path = $filesystemEntity->getPath().'/'.$filesystemEntity->getBaseName();

        while (strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }

        return ltrim($path, '/');
    }

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
