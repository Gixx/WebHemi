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
    public const OPTION_LIMIT = 'limit';
    public const OPTION_OFFSET = 'offset';
    public const OPTION_ORDER = 'order';
    public const OPTION_GROUP = 'group';
    public const OPTION_HAVING = 'having';

    /**
     * ConnectorInterface constructor.
     *
     * @param string          $name
     * @param DriverInterface $dataDriver
     */
    public function __construct(string $name, DriverInterface $dataDriver);

    /**
     * Returns the name of the connector.
     *
     * @return string
     */
    public function getConnectorName() : string;

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
     * @param array $options
     * @return array
     */
    public function getDataSet(array $expression, array $options = []) : array;

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
     * @param null|int $identifier
     * @param array $data
     * @return int The ID of the saved entity in the storage
     */
    public function saveData(? int $identifier, array $data = []) : int;

    /**
     * Removes an entity from the storage.
     *
     * @param int $identifier
     * @return bool
     */
    public function deleteData(int $identifier) : bool;
}
