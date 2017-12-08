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
use WebHemi\Data\Entity;
use WebHemi\Middleware\Action\Website\IndexAction;

/**
 * Class TagAction.
 */
class TagAction extends IndexAction
{
    /** @var string */
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
        /** @var string $category */
        $category = $parameters['uri_parameter'] ?? null;

        if (!$category) {
            throw new RuntimeException('Forbidden', 403);
        }

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        $tagEntity = $this->getFilesystemTagStorage()
            ->getFilesystemTagByApplicationAndName(
                $applicationEntity->getApplicationId(),
                $category
            );

        if (!$tagEntity) {
            throw new RuntimeException('Not Found', 404);
        }

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getFilesystemSetByApplicationAndTag(
                $applicationEntity->getApplicationId(),
                $tagEntity->getFilesystemTagId()
            );

        if (!$publications) {
            $this->templateName = 'website-post-list-empty';
        }

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
        }

        return [
            'page' => [
                'title' => $tagEntity->getTitle(),
                'type' => 'Tags',
            ],
            'activeMenu' => $category,
            'blogPosts' => $blogPosts,
        ];
    }
}
