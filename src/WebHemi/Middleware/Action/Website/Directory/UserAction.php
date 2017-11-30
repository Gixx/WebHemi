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

namespace WebHemi\Middleware\Action\Website\Directory;

use RuntimeException;
use WebHemi\Data\StorageInterface;
use WebHemi\Data\Storage;
use WebHemi\Data\Entity;
use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\Website\IndexAction;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Router\ProxyInterface;
use WebHemi\StorageTrait;

/**
 * Class UserAction
 */
class UserAction extends IndexAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-user';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $parameters = $this->getRoutingParameters();
        $userName = $parameters['uri_parameter'] ?? '';
        $user = $this->getUser($userName);
        $userMeta = $this->getUserMeta((int)$user->getUserId());

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

            // Skip publications from other users
            if ($documentEntity->getAuthorId() != $user->getUserId()) {
                continue;
            }

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
            'user' => $user,
            'userMeta' => $userMeta,
            'blogPosts' => $blogPosts,
        ];
    }

    /**
     * @param string $userName
     * @return null|Entity\User\UserEntity
     */
    private function getUser(string $userName) : ? Entity\User\UserEntity
    {
        return $this->getUserStorage()->getUserByUserName($userName);
    }

    /**
     * Gets all the meta entities for a user.
     *
     * @param int $userId
     * @return Entity\User\UserMetaEntity[]
     */
    private function getUserMeta(int $userId) : array
    {
        return $this->getUserMetaStorage()->getUserMetaForUserId($userId, true);
    }
}
