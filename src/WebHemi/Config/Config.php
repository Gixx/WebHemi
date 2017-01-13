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
declare(strict_types=1);

namespace WebHemi\Config;

use InvalidArgumentException;

/**
 * Class Config.
 */
class Config implements ConfigInterface
{
    /** @var array */
    private $pathMap = [];
    /** @var array */
    private $rawConfig;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->rawConfig = $config;
        $this->processConfig('', $config);
    }

    /**
     * Processes the config into a one dimensional array.
     *
     * @param string $path
     * @param array $config
     * @return void
     */
    private function processConfig(string $path, array $config) : void
    {
        foreach ($config as $key => $value) {
            $this->pathMap[$path.$key] = $value;

            if (is_array($value) && !empty($value)) {
                $this->processConfig($path.$key.'/', $value);
            }
        }
    }

    /**
     * Checks whether the key-path does exist or not.
     *
     * @param string $path
     * @return bool
     */
    public function has(string $path) : bool
    {
        return isset($this->pathMap[$path]);
    }

    /**
     * Retrieves configuration data for a specific key.
     *
     * @param string $path
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function getData(string $path)
    {
        if (!$this->has($path)) {
            throw new InvalidArgumentException(sprintf('Configuration for path "%s" not found', $path), 1000);
        }

        return $this->pathMap[$path];
    }

    /**
     * Returns the configuration instance for a specific key. Also add the possibility to merge additional information
     * into it.
     *
     * @param string $path
     * @throws InvalidArgumentException
     * @return ConfigInterface
     */
    public function getConfig(string $path) : ConfigInterface
    {
        if (!$this->has($path)) {
            throw new InvalidArgumentException(sprintf('Configuration for path "%s" not found', $path), 1001);
        }

        return new self($this->getData($path));
    }

    /**
     * Returns the stored raw config array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->rawConfig;
    }
}
