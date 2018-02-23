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

namespace WebHemi\Data\Entity;

use InvalidArgumentException;

/**
 * Class AbstractEntity
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var array
     */
    protected $container = [];

    /**
     * Returns entity data as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->container;
    }

    /**
     * Fills entity from an arrray.
     *
     * @param array $arrayData
     */
    public function fromArray(array $arrayData): void
    {
        foreach ($arrayData as $key => $value) {
            if (!array_key_exists($key, $this->container)) {
                throw new InvalidArgumentException(
                    sprintf('"%s" is not defined in ' . get_called_class(), $key),
                    1000
                );
            }

            $this->container[$key] = $value;
        }
    }
}
