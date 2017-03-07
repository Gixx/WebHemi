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
declare(strict_types = 1);

namespace WebHemi\Data;

/**
 * Interface ConnectorInterface.
 */
interface ConnectorInterface
{
    /**
     * ConnectorInterface constructor.
     *
     * @param DriverInterface $dataDriver
     */
    public function __construct(DriverInterface $dataDriver);

    /**
     * Returns the DriverInterface instance.
     *
     * @return DriverInterface
     */
    public function getDataDriver() : DriverInterface;

    /**
     * Set adapter data group. For Databases this can be the Tables.
     *
     * @param string $dataGroup
     * @return ConnectorInterface
     */
    public function setDataGroup(string $dataGroup) : ConnectorInterface;

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @return ConnectorInterface
     */
    public function setIdKey(string $idKey) : ConnectorInterface;

    /**
     * Get exactly one "row" of data according to the identifier.
     *
     * @param int $identifier
     * @return array
     */
    public function getData(int $identifier) : array;

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param int   $limit
     * @param int   $offset
     * @return array
     */
    public function getDataSet(array $expression, int $limit = PHP_INT_MAX, int $offset = 0) : array;

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     * @return int
     */
    public function getDataCardinality(array $expression) : int;

    /**
     * Insert or update entity in the storage.
     *
     * @param int $identifier
     * @param array $data
     * @return int The ID of the saved entity in the storage
     */
    public function saveData(? int $identifier, array $data) : int;

    /**
     * Removes an entity from the storage.
     *
     * @param int $identifier
     * @return bool
     */
    public function deleteData(int $identifier) : bool;
}
