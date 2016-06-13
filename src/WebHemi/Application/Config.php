<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Application;

// TODO make it useful for handle modules etc.

/**
 * Class Config.
 */
class Config implements ConfigInterface
{
    /** @var array */
    private $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
