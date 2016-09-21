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

use InvalidArgumentException;
use PDO;
use PDOStatement;
use RuntimeException;
use WebHemi\Adapter\Data\DataAdapterInterface;

/**
 * Class PDOAdapter.
 */
class PDOAdapter implements DataAdapterInterface
{
    /** @var PDO */
    private $dataStorage;
    /** @var string */
    private $dataGroup = null;
    /** @var string */
    private $idKey = null;

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
     * @throws RuntimeException
     *
     * @return PDOAdapter
     */
    public function setDataGroup($dataGroup)
    {
        if (!empty($this->dataGroup)) {
            throw new RuntimeException('Can\'t re-initialize dataGroup property. Property is already set.');
        }

        $this->dataGroup = $dataGroup;

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     *
     * @throws RuntimeException
     *
     * @return PDOAdapter
     */
    public function setIdKey($idKey)
    {
        if (!empty($this->idKey)) {
            throw new RuntimeException('Can\'t re-initialize idKey property. Property is already set.');
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
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getData($identifier)
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression([$this->idKey => $identifier], $queryBind, 1, 0);
        $statement = $this->getDataStorage()->prepare($query);
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

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
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getDataSet(array $expression, $limit = PHP_INT_MAX, $offset = 0)
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBind, $limit, $offset);
        $statement = $this->getDataStorage()->prepare($query);
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     *
     * @return int
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getDataCardinality(array $expression)
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBind);
        $statement = $this->getDataStorage()->prepare($query);
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * Builds SQL query from the expression.
     *
     * @param array $expression
     * @param array $queryBind
     * @param int   $limit
     * @param int   $offset
     *
     * @return string
     */
    private function getSelectQueryForExpression(
        array $expression,
        array &$queryBind,
        $limit = self::DATA_SET_RECORD_LIMIT,
        $offset = 0
    ) {
        $query = "SELECT * FROM {$this->dataGroup}";

        // Prepare WHERE expression.
        if (!empty($expression)) {
            $query .= $this->getWhereExpression($expression, $queryBind);
        }

        $query .= " LIMIT {$limit}";
        $query .= " OFFSET {$offset}";

        return $query;
    }

    /**
     * Creates a WHERE expression for the SQL query.
     *
     * @param array $expression
     * @param array $queryBind
     *
     * @return string
     */
    private function getWhereExpression(array $expression, array &$queryBind)
    {
        $whereExpression = '';
        $queryParams = [];

        foreach ($expression as $column => $value) {
            // allow special cases
            // @example  ['my_column LIKE ?' => 'some value%']
            $queryParams[] = strpos($column, '?') === false ? "{$column}=?" : $column;
            $queryBind[] = $value;
        }

        if (!empty($queryParams)) {
            $whereExpression = ' WHERE '.implode(' AND ', $queryParams);
        }

        return $whereExpression;
    }

    /**
     * Insert or update entity in the storage.
     *
     * @param mixed $identifier
     * @param array $data
     *
     * @return mixed The ID of the saved entity in the storage
     *
     * @codeCoverageIgnore Don't test external library.
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
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return empty($identifier) ? $this->getDataStorage()->lastInsertId() : $identifier;
    }

    /**
     * Binds values to the statement.
     *
     * @param PDOStatement $statement
     * @param array        $queryBind
     *
     * @codeCoverageIgnore Don't test external library.
     */
    private function bindValuesToStatement(PDOStatement &$statement, array $queryBind)
    {
        foreach ($queryBind as $index => $data) {
            $paramType = PDO::PARAM_STR;

            if (is_null($data)) {
                $paramType = PDO::PARAM_NULL;
            } elseif (is_numeric($data)) {
                $paramType = PDO::PARAM_INT;
            }

            $statement->bindValue($index + 1, $data, $paramType);
        }
    }

    /**
     * Removes an entity from the storage.
     *
     * @param mixed $identifier
     *
     * @return bool
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function deleteData($identifier)
    {
        $statement = $this->getDataStorage()->prepare("DELETE FROM WHERE {$this->idKey}=?");

        return $statement->execute([$identifier]);
    }
}
