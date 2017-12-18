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

use WebHemi\Data\Entity;
use WebHemi\Middleware\Action\Website\IndexAction;

/**
 * Class GalleryAction.
 */
class GalleryAction extends IndexAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-image-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        $blogPosts = [];

        return [
            'page' => [
                'title' => '',
                'type' => 'Gallery',
            ],
            'activeMenu' => '',
            'application' => $this->getApplicationData($applicationEntity),
            'blogPosts' => $blogPosts,
        ];
    }
}
