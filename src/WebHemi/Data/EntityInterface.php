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

namespace WebHemi\Data;

/**
 * Interface EntityInterface.
 *
 * A Data Entity represents a unique set of data, or a row in a database table.
 */
interface EntityInterface
{
    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return EntityInterface - declare specific return type in the implementations
     */
    public function setKeyData(int $entityId);

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int;
}
