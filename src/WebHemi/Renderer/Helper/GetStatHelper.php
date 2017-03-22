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

namespace WebHemi\Renderer\Helper;

use WebHemi\Renderer\HelperInterface;

/**
 * Class GetStatHelper
 *
 * @codeCoverageIgnore - config and PHP core functions. No business logic.
 */
class GetStatHelper implements HelperInterface
{
    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'getStat';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getStat() }}';
    }

    /**
     * Gets helper options for the render.
     *
     * @return array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return 'Triggers the WebHemi\'s built-in timer and returns the data in array:'.PHP_EOL
            . '\'start_time\': the time when the timer had been called (called automatically).'.PHP_EOL
            . '\'end_time\': the time when the timer had been stopped (called in a template)'.PHP_EOL
            . '\'duration\': the difference in seconds '.PHP_EOL
            . '\'memory\': the maximum memory usage during the render in a human readable format '.PHP_EOL;
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        return render_stat();
    }
}
