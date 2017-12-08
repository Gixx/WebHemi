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

namespace WebHemi\Middleware\Action\Traits;

use WebHemi\Data\Entity;

/**
 * Trait GetPublicationPathTrait
 */
trait GetPublicationPathTrait
{
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
}
