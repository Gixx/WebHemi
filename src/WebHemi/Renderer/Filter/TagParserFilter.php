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

namespace WebHemi\Renderer\Filter;

use WebHemi\Renderer\FilterInterface;
use WebHemi\Renderer\TagFilterInterface;

/**
 * Class TagParserFilter
 */
class TagParserFilter implements FilterInterface
{
    /** @var TagFilterInterface[] */
    private $tagFilters;

    /**
     * TagParserFilter constructor.
     *
     * @param TagFilterInterface[] ...$tagFilterInterfaces
     */
    public function __construct(TagFilterInterface ...$tagFilterInterfaces)
    {
        foreach ($tagFilterInterfaces as $instance) {
            $filterClass = get_class($instance);

            $this->tagFilters[$filterClass] = $instance;
        }
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     *
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'tagParser';
    }

    /**
     * Should return the definition of the helper.
     *
     * @return string
     *
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{{ "some text with [TAG] in the content"|tagParser }}';
    }

    /**
     * Should return a description text.
     *
     * @return string
     *
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Search the input text for available tags and replaces them with action outputs';
    }

    /**
     * Gets filter options for the render.
     *
     * @return array
     *
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
     * @param string
     * @return string
     */
    public function __invoke() : string
    {
        $text = func_get_args()[0] ?? '';

        $matches = [];

        if (preg_match_all('/\#(\w+)\#/', $text, $matches)) {
            foreach ($matches[1] as $tagFilter) {
                $className = __NAMESPACE__.'\\Tags\\'.$tagFilter;

                if (isset($this->tagFilters[$className])) {
                    /** @var TagFilterInterface $filter */
                    $filter = $this->tagFilters[$className];
                    $text = $filter->filter($text);
                }
            }
        }

        return $text;
    }
}
