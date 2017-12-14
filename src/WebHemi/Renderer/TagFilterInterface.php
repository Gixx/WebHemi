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

namespace WebHemi\Renderer;

/**
 * Interface TagFilterInterface
 */
interface TagFilterInterface
{
    /**
     * Apply the filter.
     *
     * @param string $text
     * @return string
     */
    public function filter(string $text) : string;
}
