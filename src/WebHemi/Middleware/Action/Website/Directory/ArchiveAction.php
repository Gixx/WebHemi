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

use WebHemi\Data\StorageInterface;
use WebHemi\Data\Storage;
use WebHemi\Data\Entity;
use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Router\ProxyInterface;
use WebHemi\StorageTrait;

/**
 * Class ArchiveAction.
 */
class ArchiveAction extends AbstractMiddlewareAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-post-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $blogPosts = [];
        $title = '';
        $parameters = $this->getRoutingParameters();


        return [
            'title' => $title,
            'activeMenu' => $parameters['uri_parameter'],
            'blogPosts' => $blogPosts,
        ];
    }
}
