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

namespace WebHemi\Middleware\Action\Website\Directory;

use RuntimeException;
use WebHemi\Data\Entity;
use WebHemi\Middleware\Action\Website\IndexAction;

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
        $blogPosts = [];
        $parameters = $this->getRoutingParameters();

        $userName = $parameters['uri_parameter'] ?? '';

        if (empty($userName)) {
            throw new RuntimeException('Forbidden', 403);
        }

        /** @var Entity\User\UserEntity $userEntity */
        $userEntity = $this->getUserStorage()
            ->getUserByUserName($userName);
        /** @var array $userMeta */
        $userMeta = $this->getUserMetaStorage()
            ->getUserMetaArrayForUserId((int) $userEntity->getUserId());

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getPublishedDocumentsByAuthor($applicationEntity->getApplicationId(), $userEntity->getUserId());

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
        }

        return [
            'activeMenu' => '',
            'user' => [
                'userId' => $userEntity->getUserId(),
                'userName' => $userEntity->getUserName(),
                'url' => $this->environmentManager->getRequestUri(),
                'meta' => $userMeta,
            ],
            'application' => $this->getApplicationData($applicationEntity),
            'blogPosts' => $blogPosts,
        ];
    }
}
