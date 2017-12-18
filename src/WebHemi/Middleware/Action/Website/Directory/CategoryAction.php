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
 * Class CategoryAction.
 */
class CategoryAction extends IndexAction
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
        $category = $parameters['uri_parameter'] ?? '';

        if (empty($category)) {
            throw new RuntimeException('Forbidden', 403);
        }

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /** @var Entity\Filesystem\FilesystemCategoryEntity $categoryEntity */
        $categoryEntity = $this->getFilesystemCategoryStorage()
            ->getFilesystemCategoryByApplicationAndName(
                $applicationEntity->getApplicationId(),
                $category
            );

        if (!$categoryEntity instanceof Entity\Filesystem\FilesystemCategoryEntity) {
            throw new RuntimeException('Not Found', 404);
        }

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getPublishedDocuments(
                $applicationEntity->getApplicationId(),
                [
                    'fk_category = ?' => (int) $categoryEntity->getFilesystemCategoryId(),
                ]
            );

        if (empty($publications)) {
            $this->templateName = 'website-post-list-empty';
        }

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
        }

        return [
            'page' => [
                'title' => $categoryEntity->getTitle(),
                'name' => $categoryEntity->getName(),
                'description' => $categoryEntity->getDescription(),
                'type' => 'Categories',
            ],
            'activeMenu' => $category,
            'application' => $this->getApplicationData($applicationEntity),
            'blogPosts' => $blogPosts,
        ];
    }
}
