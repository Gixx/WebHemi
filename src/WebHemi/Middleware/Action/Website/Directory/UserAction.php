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

        $userName = $parameters['basename'] ?? '';

        if ($parameters['path'] == '/' || empty($userName)) {
            throw new RuntimeException('Forbidden', 403);
        }

        /**
         * @var Entity\UserEntity $userEntity
         */
        $userEntity = $this->getUserStorage()
            ->getUserByUserName($userName);

        /**
         * @var array $userMeta
         */
        $userMeta = $this->getUserStorage()
            ->getSimpleUserMetaListByUser((int) $userEntity->getUserId());

        /**
         * @var Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /**
         * @var Entity\EntitySet $publications
         */
        $publications = $this->getFilesystemStorage()
            ->getFilesystemPublishedDocumentListByAuthor(
                (int) $applicationEntity->getApplicationId(),
                (int) $userEntity->getUserId()
            );

        /**
         * @var Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
         */
        foreach ($publications as $publishedDocumentEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $publishedDocumentEntity);
        }

        return [
            'activeMenu' => '',
            'user' => [
                'userId' => $userEntity->getUserId(),
                'userName' => $userEntity->getUserName(),
                'url' => $this->environmentManager->getRequestUri(),
                'meta' => $userMeta,
            ],
            'Application' => $applicationEntity,
            'blogPosts' => $blogPosts,
        ];
    }
}
