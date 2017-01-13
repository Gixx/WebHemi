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
declare(strict_types=1);

namespace WebHemi\Middleware;

/**
 * Interface MiddlewareActionInterface.
 */
interface MiddlewareActionInterface
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string;

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array;
}
