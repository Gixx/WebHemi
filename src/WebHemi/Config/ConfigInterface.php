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
namespace WebHemi\Config;

use InvalidArgumentException;

/**
 * Interface ConfigInterface.
 */
interface ConfigInterface
{
    /**
     * ConfigInterface constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Checks whether the key-path does exist or not.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path);

    /**
     * Returns the configuration data for a specific key.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function getData($path);

    /**
     * Returns the configuration instance for a specific key. Also add the possibility to merge additional information
     * into it.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     *
     * @return ConfigInterface
     */
    public function getConfig($path);

    /**
     * Returns the stored raw config array.
     *
     * @return array
     */
    public function toArray();
}
