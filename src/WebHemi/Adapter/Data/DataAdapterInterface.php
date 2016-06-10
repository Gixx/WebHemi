<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\Adapter\Data;

/**
 * Interface DataAdapterInterface
 * @package WebHemi\Adapter\Data
 */
interface DataAdapterInterface
{
    /**
     * PDOAdapter constructor.
     * @param mixed $dataStorage
     */
    public function __construct($dataStorage = null);

    /**
     * Returns the Data Storage instance.
     *
     * @return mixed
     */
    public function getDataStorage();

    /**
     * Set adapter data group. For Databases this can be the Tables.
     *
     * @param string $dataGroup
     * @return DataAdapterInterface
     */
    public function setDataGroup($dataGroup);

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @return DataAdapterInterface
     */
    public function setIdKey($idKey);

    /**
     * Get exactly one "row" of data according to the expression.
     *
     * @param mixed $id
     * @return array
     */
    public function getData($id);

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getDataSet(array $expression, $limit = null, $offset = null);

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     * @return int
     */
    public function getDataCardinality(array $expression);

    /**
     * Insert or update entity in the storage
     *
     * @param mixed $id
     * @param array $data
     * @return mixed The ID of the saved entity in the storage
     */
    public function saveData($id, array $data);

    /**
     * Removes an entity from the storage
     *
     * @param int $id
     * @return boolean
     */
    public function deleteData($id);
}
