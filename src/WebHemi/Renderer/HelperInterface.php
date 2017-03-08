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
 * Interface HelperInterface
 */
interface HelperInterface
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
     * A renderer helper should be called with its name.
     *
     * @return mixed
     */
    public function __invoke();
}
