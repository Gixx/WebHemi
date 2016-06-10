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

namespace WebHemi\Adapter\Data\InMemory;

use WebHemi\Adapter\Data\DataAdapterInterface;

/**
 * Class InMemoryAdapter
 * @package WebHemi\Adapter\Data\InMemory
 */
class InMemoryAdapter implements DataAdapterInterface
{
    /** @var array mixed */
    private $dataStorage;
    /** @var  string */
    protected $dataGroup = null;
    /** @var null  */
    protected $idKey = null;

    /**
     * PDOAdapter constructor.
     * @param mixed $dataStorage
     */
    public function __construct($dataStorage = null)
    {
        $this->dataStorage = [];
    }

    /**
     * Returns the Data Storage instance.
     *
     * @return PDO
     */
    public function getDataStorage()
    {
        return $this->dataStorage;
    }

    /**
     * Set adapter data group.
     *
     * @param string $dataGroup
     * @return InMemoryAdapter
     */
    public function setDataGroup($dataGroup)
    {
        $this->dataGroup = $dataGroup;

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @return InMemoryAdapter
     */
    public function setIdKey($idKey)
    {
        $this->idKey = $idKey;

        return $this;
    }

    /**
     * Get exactly one "row" of data according to the expression.
     *
     * @param mixed $id
     * @return array
     */
    public function getData($id)
    {
        $result = [];

        // todo implement SELECT query

        return $result;
    }

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getDataSet(array $expression, $limit = null, $offset = null)
    {
        $entityList = [];

        // todo implement SELECT query

        return $entityList;
    }

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     * @return int
     */
    public function getDataCardinality(array $expression)
    {
        $list = $this->getDataSet($expression);

        return count($list);
    }

    /**
     * Insert or update entity in the storage
     *
     * @param mixed $id
     * @param array $data
     * @return mixed The ID of the saved entity in the storage
     */
    public function saveData($id, array $data)
    {
        $returnId = 0;

        // todo implement INSERT / UPDATE queries

        return $returnId;
    }

    /**
     * Removes an entity from the storage
     *
     * @param int $id
     * @return boolean
     */
    public function deleteData($id)
    {
        $result = false;

        // todo implement DELETE query

        return $result;
    }
}
