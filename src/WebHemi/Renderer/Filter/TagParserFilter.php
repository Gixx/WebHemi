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

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\FilterInterface;

/**
 * Class TagParserFilter
 */
class TagParserFilter implements FilterInterface
{
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var EnvironmentInterface */
    private $environmentManager;

    private $allowedTags = [
        'URL' => 'getCurrentUri',
    ];

    /**
     * TagParserFilter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(ConfigurationInterface $configuration, EnvironmentInterface $environmentManager)
    {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'tagParser';
    }

    /**
     * Should return the definition of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ "some text with [TAG] in the content"|tagParser }}';
    }

    /**
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return 'Search the input text for available tags and replaces them with action outputs';
    }

    /**
     * Gets filter options for the render.
     *
     * @return array
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

        foreach ($this->allowedTags as $tagName => $action) {
            if (strpos($text, '['.$tagName.']') !== false) {
                $this->$action($text);
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @return $this
     */
    private function getCurrentUri(string &$text)
    {
        $uri = rtrim($this->environmentManager->getRequestUri(), '/');
        $text = str_replace('[URL]', $uri, $text);

        return $this;
    }
}
