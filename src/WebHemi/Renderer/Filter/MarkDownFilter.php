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

use WebHemi\Parser\ServiceInterface as ParserInterface;
use WebHemi\Renderer\FilterInterface;

/**
 * Class MarkDownFilter
 *
 * @codeCoverageIgnore - See tests for the WebHemi\Parser\ServiceAdapter\Parsedown\ServiceAdapter::class
 */
class MarkDownFilter implements FilterInterface
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * MarkDownFilter constructor.
     *
     * @param ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'markdown';
    }

    /**
     * Should return the definition of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{{ "some **markdown** text"|markdown }}';
    }

    /**
     * Should return a description text.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Parses the input text as a markdown script and outputs the HTML.';
    }

    /**
     * Gets filter options for the render.
     *
     * @return array
     * @codeCoverageIgnore - fix array
     */
    public static function getOptions() : array
    {
        return ['is_safe' => ['html']];
    }

    /**
     * Parses the input text as a markdown script and outputs the HTML.
     *
     * @return string
     */
    public function __invoke() : string
    {
        $text = func_get_args()[0] ?? '';

        $content = $this->parser->parse($text);
        $content = str_replace(
            ['<table>', '</table>'],
            ['<div class="table"><table>', '</table></div>'],
            $content
        );

        return $content;
    }
}
