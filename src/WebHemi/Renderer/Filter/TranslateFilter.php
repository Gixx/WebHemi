<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Renderer\Filter;

use WebHemi\I18n\DriverInterface;
use WebHemi\Renderer\FilterInterface;

/**
 * Class TranslateFilter
 */
class TranslateFilter implements FilterInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * TranslateFilter constructor.
     *
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 't';
    }

    /**
     * Should return the definition of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{{ "Some %s in English"|t("idea") }}';
    }

    /**
     * Should return a description text.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Translates the text into the language configured for the application.';
    }

    /**
     * Gets filter options for the render.
     *
     * @return array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * Parses the input text as a markdown script and outputs the HTML.
     *
     * @uses TagParserFilter::getCurrentUri()
     *
     * @return string
     */
    public function __invoke() : string
    {
        $arguments = func_get_args() ?? [];
        $text = array_shift($arguments);
        $text = $this->driver->translate($text);

        if (!empty($arguments)) {
            array_unshift($arguments, $text);
            $text = call_user_func_array('sprintf', $arguments);
        }

        return $text;
    }
}
