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
namespace WebHemi\Adapter\Data\PDO;

use PDO;
use PDOStatement;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Exception\InitException;
use WebHemi\Adapter\Exception\InvalidArgumentException;

/**
 * Class PDOAdapter.
 */
class PDOAdapter implements DataAdapterInterface
{
    /** @var PDO */
    private $dataStorage;
    /** @var string */
    protected $dataGroup = null;
    /** @var string */
    protected $idKey = null;

    /**
     * PDOAdapter constructor.
     *
     * @param PDO $dataStorage
     *
     * @throws InvalidArgumentException
     */
    public function __construct($dataStorage = null)
    {
        if (!$dataStorage instanceof PDO) {
            $type = gettype($dataStorage);

            if ($type == 'object') {
                $type = get_class($dataStorage);
            }

            $message = sprintf(
                'Can\'t create %s instance. The parameter must be an instance of PDO, %s given.',
                __CLASS__,
                $type
            );

            throw new InvalidArgumentException($message);
        }

        $this->dataStorage = $dataStorage;
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
     * Set adapter data group. For Databases this can be the Tables.
     *
     * @param string $dataGroup
     *
     * @throws InitException
     *
     * @return PDOAdapter
     */
    public function setDataGroup($dataGroup)
    {
        if (!empty($this->dataGroup)) {
            throw new InitException('Can\'t re-initialize dataGroup property. Property is already set.');
        }

        $this->dataGroup = $dataGroup;

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     *
     * @throws InitException
     *
     * @return PDOAdapter
     */
    public function setIdKey($idKey)
    {
        if (!empty($this->idKey)) {
            throw new InitException('Can\'t re-initialize idKey property. Property is already set.');
        }

        $this->idKey = $idKey;

        return $this;
    }

    /**
     * Get exactly one "row" of data according to the expression.
     *
     * @param mixed $identifier
     *
     * @return array
     */
    public function getData($identifier)
    {
        $statement = $this->getDataStorage()->prepare("SELECT * FROM {$this->dataGroup} WHERE {$this->idKey}=?");
        $statement->execute([$identifier]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param int   $limit
     * @param int   $offset
     *
     * @return array
     */
    public function getDataSet(array $expression, $limit = null, $offset = null)
    {
        $statement = $this->getStatementForExpression($expression, $limit, $offset);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     *
     * @return int
     */
    public function getDataCardinality(array $expression)
    {
        $statement = $this->getStatementForExpression($expression);
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * Build query statement from the expression.
     *
     * @param array $expression
     * @param int   $limit
     * @param int   $offset
     *
     * @return PDOStatement
     */
    protected function getStatementForExpression(array $expression, $limit = null, $offset = null)
    {
        $query = "SELECT * FROM {$this->dataGroup}";
        $queryParams = [];
        $queryBind = [];

        // Prepare WHERE expression.
        if (!empty($expression)) {
            $query .= ' WHERE ';

            foreach ($expression as $column => $value) {
                // allow special cases
                // @example  ['my_column LIKE ?' => 'some value%']
                $queryParams[] = strpos($column, '?') === false ? "{$column}=?" : $column;
                $queryBind[] = $value;
            }

            $query .= implode(' AND ', $queryParams);
        }

        // Prepare LIMIT and OFFSET
        if (!empty($limit)) {
            $query .= " LIMIT {$limit}";

            if (!is_null($offset)) {
                $query .= " OFFSET {$offset}";
            }
        }

        $statement = $this->getDataStorage()->prepare($query);

        foreach ($queryBind as $index => $data) {
            $paramType = PDO::PARAM_STR;

            if (is_null($data)) {
                $paramType = PDO::PARAM_NULL;
            } elseif (is_numeric($data)) {
                $paramType = PDO::PARAM_INT;
            }

            $statement->bindValue($index + 1, $data, $paramType);
        }

        return $statement;
    }

    /**
     * Insert or update entity in the storage.
     *
     * @param mixed $identifier
     * @param array $data
     *
     * @return mixed The ID of the saved entity in the storage
     */
    public function saveData($identifier, array $data)
    {
        if (empty($identifier)) {
            $query = "INSERT INTO {$this->dataGroup}";
        } else {
            $query = "UPDATE {$this->dataGroup}";
        }

        $queryData = [];
        $queryBind = [];

        foreach ($data as $fieldName => $value) {
            $queryData[] = "{$fieldName}=?";
            $queryBind[] = $value;
        }

        $query .= ' SET '.implode(', ', $queryData);

        if (!empty($identifier)) {
            $query .= " WHERE {$this->idKey}=?";
            $queryBind[] = $identifier;
        }

        $statement = $this->getDataStorage()->prepare($query);

        foreach ($queryBind as $index => $data) {
            $paramType = PDO::PARAM_STR;

            if (is_null($data)) {
                $paramType = PDO::PARAM_NULL;
            } elseif (is_numeric($data)) {
                $paramType = PDO::PARAM_INT;
            }

            $statement->bindValue($index + 1, $data, $paramType);
        }

        $statement->execute();

        return empty($identifier) ? $this->getDataStorage()->lastInsertId() : $identifier;
    }

    /**
     * Removes an entity from the storage.
     *
     * @param mixed $identifier
     *
     * @return bool
     */
    public function deleteData($identifier)
    {
        $statement = $this->getDataStorage()->prepare("DELETE FROM WHERE {$this->idKey}=?");

        return $statement->execute([$identifier]);
    }
}
