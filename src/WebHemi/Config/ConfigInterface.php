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
    const CONFIG_AS_OBJECT = 1;
    const CONFIG_AS_ARRAY = 2;

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
     * Returns the configuration for a specific key.
     *
     * @param string $path
     * @param int    $returnType
     *
     * @throws InvalidArgumentException
     *
     * @return array|Config
     */
    public function get($path, $returnType = self::CONFIG_AS_ARRAY);

    /**
     * Returns the stored raw config array.
     *
     * @return array
     */
    public function toArray();
}
