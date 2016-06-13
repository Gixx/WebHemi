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

/**
 * Interface ConfigInterface.
 */
interface ConfigInterface
{
    /**
     * ConfigInterface constructor. Sets the 'core' configuration.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Returns the configuration.
     *
     * @return array
     */
    public function getConfig();
}
