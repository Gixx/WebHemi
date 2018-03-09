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

namespace WebHemi\Middleware\Action\Website\View;

use RuntimeException;
use WebHemi\Data\Entity;
use WebHemi\Middleware\Action\Website\IndexAction;

/**
 * Class PostAction
 */
class PostAction extends IndexAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-post-view';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $routingParams = $this->getRoutingParameters();

        /**
         * @var Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /**
         * @var null|Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
         */
        $publishedDocumentEntity = $this->getFilesystemStorage()
            ->getFilesystemPublishedDocumentByApplicationAndPath(
                (int) $applicationEntity->getApplicationId(),
                $routingParams['path'],
                $routingParams['basename']
            );

        if (!$publishedDocumentEntity instanceof Entity\FilesystemPublishedDocumentEntity) {
            throw new RuntimeException('Page not found', 404);
        }

        return [
            'activeMenu' => '',
            'page' => [
                'type' => 'Categories',
            ],
            'application' => $this->getApplicationData($applicationEntity),
            'blogPost' => $this->getBlobPostData($applicationEntity, $publishedDocumentEntity),
        ];
    }
}
