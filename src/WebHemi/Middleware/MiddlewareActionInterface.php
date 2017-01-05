<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
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
    public function getTemplateName();

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData();
}
