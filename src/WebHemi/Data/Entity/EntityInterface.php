<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity;

/**
 * Interface EntityInterface
 */
interface EntityInterface
{
    /**
     * Returns entity data as an array.
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Fills entity from an arrray.
     *
     * @param array $arrayData
     */
    public function fromArray(array $arrayData) : void;
}
