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
namespace WebHemi\Data\Entity;

/**
 * Interface DataEntityInterface.
 *
 * A Data Entity represents a unique set of data, or a row in a database table.
 */
interface DataEntityInterface
{
    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData();
}
