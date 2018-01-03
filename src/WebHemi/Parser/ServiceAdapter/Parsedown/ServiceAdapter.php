<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Parser\ServiceAdapter\Parsedown;

use Parsedown;
use WebHemi\Parser\ServiceInterface;

/**
 * Class ServiceAdapter
 *
 * @codeCoverageIgnore - no need to test external library
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var Parsedown */
    private $adapter;

    /**
     * ServiceAdapter constructor.
     */
    public function __construct()
    {
        $this->adapter = new Parsedown();
    }

    /**
     * Parses the input.
     *
     * @param string $text
     * @return string
     */
    public function parse(string $text) : string
    {
        return $this->adapter->text($text);
    }
}
