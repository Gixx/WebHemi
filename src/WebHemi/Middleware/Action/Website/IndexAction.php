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

namespace WebHemi\Middleware\Action\Website;

use WebHemi\Data\StorageInterface;
use WebHemi\Data\Storage;
use WebHemi\Data\Entity;
use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Router\ProxyInterface;
use WebHemi\StorageTrait;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var EnvironmentInterface */
    protected $environmentManager;

    use StorageTrait;

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
            /** @var Entity\Filesystem\FilesystemDocumentEntity $documentEntity */
            $documentEntity = $this->getFilesystemDocumentStorage()
                ->getFilesystemDocumentById($filesystemEntity->getDocumentId());

            $documentMeta = $this->getFilesystemStorage()
                ->getPublicationMeta($filesystemEntity->getFilesystemId());

            $author = $this->getPublicationAuthor(
                $documentEntity->getAuthorId(),
                $applicationEntity->getApplicationId()
            );
            $author['mood'] = [];

            if (isset($documentMeta['mood_key']) && isset($documentMeta['mood_name'])) {
                $author['mood'] = [
                    $documentMeta['mood_name'],
                    $documentMeta['mood_key']
                ];
            }

            $blogPosts[] = [
                'author' => $author,
                'tags' => $this->getPublicationTags(
                    $applicationEntity->getApplicationId(),
                    $filesystemEntity->getFilesystemId()
                ),
                'category' => $this->getPublicationCategory(
                    $applicationEntity->getApplicationId(),
                    $filesystemEntity->getCategoryId()
                ),
                'publishedAt' => $filesystemEntity->getDatePublished(),
                'location' => $documentMeta['location'] ?? '',
                'summary' => $filesystemEntity->getDescription(),
                'illustration' => $documentMeta['illustration'] ?? '',
                'path' => $this->getPublicationPath($filesystemEntity),
                'title' => $filesystemEntity->getTitle(),
                'contentLead' => $documentEntity->getContentLead(),
                'contentBody' => $documentEntity->getContentBody()
            ];
        }

        return [
            'activeMenu' => '',
            'blogPosts' => $blogPosts,
            'fixPost' => $applicationEntity->getIntroduction(),
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

        if (strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }

        return $path;
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

        if ($tagEntities) {
            /** @var array $categoryDirectoryData */
            $categoryDirectoryData = $this->getFilesystemDirectoryStorage()
                ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_TAG);

            /** @var Entity\Filesystem\FilesystemTagEntity $tagEntity */
            foreach ($tagEntities as $tagEntity) {
                $tags[] = [
                    'url' => $categoryDirectoryData['uri'].'/'.$tagEntity->getName(),
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
            'title' => $categoryEntity->getTitle()
        ];
        return $category;
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
        $user = $this->getUserStorage()
            ->getUserById($userId);

        $userMeta = $this->getUserMetaStorage()
            ->getUserMetaSetForUserId($userId);

        /** @var array $userDirectoryData */
        $userDirectoryData = $this->getFilesystemDirectoryStorage()
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_USER);

        return [
            'user_id' => $userId,
            'username' => $user->getUserName(),
            'url' => $userDirectoryData['uri'].'/'.$user->getUserName(),
            'name' => $userMeta['display_name'] ?? $user->getUserName(),
            'email' => ($userMeta['email_visible'] ?? 0) ? $user->getEmail() : '',
            'avatar' => ($userMeta['avatar_type'] ?? '') == 'gravatar'
                ? 'http://www.gravatar.com/avatar/'.md5(strtolower($userMeta['avatar'])).'?s=256&r=g'
                : $userMeta['avatar'] ?? ''
        ];
    }
}
