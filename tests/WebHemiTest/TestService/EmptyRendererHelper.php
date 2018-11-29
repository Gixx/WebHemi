<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemiTest\TestService;

use WebHemi\Renderer\HelperInterface;

/**
 * Class EmptyRendererHelper
 */
class EmptyRendererHelper implements HelperInterface
{
    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'empty';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return 'empty(void)';
    }

    /**
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return 'Does nothing; returns the arguments';
    }

    /**
     * Should return an array.
     *
     * @return array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @param array ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        return implode(' ', $arguments);
    }
}
