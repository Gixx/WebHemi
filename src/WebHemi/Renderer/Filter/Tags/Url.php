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

namespace WebHemi\Renderer\Filter\Tags;

use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\TagFilterInterface;

/**
 * Class Url
 */
class Url implements TagFilterInterface
{
    private $environmentManager;

    /**
     * Url constructor.
     *
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(EnvironmentInterface $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    /**
     * Apply the filter.
     *
     * @param  string $text
     * @return string
     */
    public function filter(string $text) : string
    {
        $uri = rtrim($this->environmentManager->getRequestUri(), '/');
        return str_replace('#Url#', $uri, $text);
    }
}
