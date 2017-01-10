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
namespace WebHemi\Data\Entity;

/**
 * Interface DataEntityInterface.
 *
 * A Data Entity represents a unique set of data, or a row in a database table.
 */
interface DataEntityInterface
{
    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return DataEntityInterface
     */
    public function setKeyData($entityId);

    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData();
}
