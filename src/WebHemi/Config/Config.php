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
     * @param $path
     * @param $config
     */
    private function processConfig($path, $config)
    {
        if (!is_array($config)) {
            return;
        }

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
     *
     * @return bool
     */
    public function has($path)
    {
        return isset($this->pathMap[$path]);
    }

    /**
     * Retrieves configuration for a specific key.
     *
     * @param string $path
     * @param int    $returnType
     *
     * @throws InvalidArgumentException
     *
     * @return array|Config
     */
    public function get($path, $returnType = self::CONFIG_AS_ARRAY)
    {
        if (!$this->has($path)) {
            throw new InvalidArgumentException(sprintf('Configuration for path "%s" not found', $path));
        }

        $config = $this->pathMap[$path];

        if (self::CONFIG_AS_OBJECT == $returnType) {
            $config = new self($config);
        }

        return $config;
    }

    /**
     * Returns the stored raw config array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->rawConfig;
    }
}
