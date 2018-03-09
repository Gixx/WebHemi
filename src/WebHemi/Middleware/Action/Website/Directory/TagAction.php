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
 * Class TagAction.
 */
class TagAction extends IndexAction
{
    /**
     * @var string
     */
    protected $templateName = 'website-post-list';

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return $this->templateName;
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
        /**
         * @var string $tagName
         */
        $tagName = $parameters['basename'] ?? null;

        if ($parameters['path'] == '/' || empty($tagName)) {
            throw new RuntimeException('Forbidden', 403);
        }

        /**
         * @var Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /**
         * @var Entity\FilesystemTagEntity $tagEntity
         */
        $tagEntity = $this->getFilesystemStorage()
            ->getFilesystemTagByApplicationAndName(
                (int) $applicationEntity->getApplicationId(),
                $tagName
            );

        if (!$tagEntity instanceof Entity\FilesystemTagEntity) {
            throw new RuntimeException('Not Found', 404);
        }

        /**
         * @var Entity\EntitySet $publications
         */
        $publications = $this->getFilesystemStorage()
            ->getFilesystemPublishedDocumentListByTag(
                (int) $applicationEntity->getApplicationId(),
                (int) $tagEntity->getFilesystemTagId()
            );

        if (empty($publications)) {
            $this->templateName = 'website-post-list-empty';
        }

        /**
         * @var Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
         */
        foreach ($publications as $publishedDocumentEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $publishedDocumentEntity);
        }

        return [
            'page' => [
                'title' => $tagEntity->getTitle(),
                'name' => $tagEntity->getName(),
                'description' => $tagEntity->getDescription(),
                'type' => 'Tags',
            ],
            'activeMenu' => $tagName,
            'application' => $this->getApplicationData($applicationEntity),
            'blogPosts' => $blogPosts,
        ];
    }
}
