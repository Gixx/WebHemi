<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Renderer;

/**
 * Interface FilterInterface
 */
interface FilterInterface
{
    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string;

    /**
     * Should return the definition of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string;

    /**
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string;

    /**
     * Gets filter options for the render.
     *
     * @return array
     */
    public static function getOptions() : array;

    /**
     * A renderer filter should be called with its name.
     *
     * @return mixed
     */
    public function __invoke();
}
