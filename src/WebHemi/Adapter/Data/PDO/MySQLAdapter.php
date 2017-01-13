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
declare(strict_types=1);

namespace WebHemi\Adapter\Data\PDO;

use InvalidArgumentException;
use PDO;
use PDOStatement;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class MySQLAdapter.
 */
class MySQLAdapter implements DataAdapterInterface
{
    /** @var PDO */
    private $dataDriver;
    /** @var string */
    private $dataGroup = null;
    /** @var string */
    private $idKey = null;

    /**
     * MySQLAdapter constructor.
     *
     * @param DataDriverInterface $dataDriver
     * @throws InvalidArgumentException
     */
    public function __construct(DataDriverInterface $dataDriver)
    {
        if (!$dataDriver instanceof PDO) {
            $type = gettype($dataDriver);

            if ($type == 'object') {
                $type = get_class($dataDriver);
            }

            $message = sprintf(
                'Can\'t create %s instance. The parameter must be an instance of PDO, %s given.',
                __CLASS__,
                $type
            );

            throw new InvalidArgumentException($message);
        }

        $this->dataDriver = $dataDriver;
    }

    /**
     * Returns the Data Storage instance.
     *
     * @return DataDriverInterface
     */
    public function getDataDriver() : DataDriverInterface
    {
        return $this->dataDriver;
    }

    /**
     * Set adapter data group. For Databases this can be the Tables.
     *
     * @param string $dataGroup
     * @return DataAdapterInterface
     */
    public function setDataGroup(string $dataGroup) : DataAdapterInterface
    {
        $this->dataGroup = $dataGroup;

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @return DataAdapterInterface
     */
    public function setIdKey(string $idKey) : DataAdapterInterface
    {
        $this->idKey = $idKey;

        return $this;
    }

    /**
     * Get exactly one "row" of data according to the expression.
     *
     * @param int $identifier
     * @return array
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getData(int $identifier) : array
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression([$this->idKey => $identifier], $queryBind, 1, 0);
        $statement = $this->dataDriver->prepare($query);
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
     * @return array
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getDataSet(array $expression, int $limit = PHP_INT_MAX, int $offset = 0) : array
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBind, $limit, $offset);
        $statement = $this->dataDriver->prepare($query);
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     * @return int
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getDataCardinality(array $expression) : int
    {
        $queryBind = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBind);
        $statement = $this->dataDriver->prepare($query);
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
     * @return string
     */
    private function getSelectQueryForExpression(
        array $expression,
        array &$queryBind,
        int $limit = PHP_INT_MAX,
        int $offset = 0
    ) : string {
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
     * @return string
     */
    private function getWhereExpression(array $expression, array &$queryBind) : string
    {
        $whereExpression = '';
        $queryParams = [];

        foreach ($expression as $column => $value) {
            if (is_array($value)) {
                $queryParams[] = $this->getInColumnCondition($column, count($value));
                $queryBind = array_merge($queryBind, $value);
            } elseif (strpos($column, ' LIKE') !== false || (is_string($value) && strpos($value, '%') !== false)) {
                $queryParams[] = $this->getLikeColumnCondition($column);
                $queryBind[] = $value;
            } else {
                $queryParams[] = $this->getSimpleColumnCondition($column);
                $queryBind[] = $value;
            }
        }

        if (!empty($queryParams)) {
            $whereExpression = ' WHERE '.implode(' AND ', $queryParams);
        }

        return $whereExpression;
    }

    /**
     * Gets a simple condition for the column.
     *
     * @param string $column
     * @return string 'my_column = ?'
     */
    private function getSimpleColumnCondition(string $column) : string
    {
        return strpos($column, '?') === false ? "{$column} = ?" : $column;
    }

    /**
     * Gets a 'LIKE' condition for the column.
     *
     * Allows special cases:
     * @example  ['my_column LIKE ?' => 'some value%']
     * @example  ['my_column LIKE' => 'some value%']
     * @example  ['my_column' => 'some value%']
     *
     * @param string $column
     * @return string 'my_column LIKE ?'
     */
    private function getLikeColumnCondition(string $column) : string
    {
        list($columnNameOnly) = explode(' ', $column);

        return $columnNameOnly.' LIKE ?';
    }

    /**
     * Gets an 'IN' condition for the column.
     *
     * Allows special cases:
     * @example  ['my_column IN (?)' => [1,2,3]]
     * @example  ['my_column IN ?' => [1,2,3]]
     * @example  ['my_column IN' => [1,2,3]]
     * @example  ['my_column' => [1,2,3]]
     *
     * @param string $column
     * @param int    $parameterCount
     * @return string 'my_column IN (?,?,?)'
     */
    private function getInColumnCondition(string $column, int $parameterCount = 1) : string
    {
        list($columnNameOnly) = explode(' ', $column);

        $inParameters = str_repeat('?,', $parameterCount - 1).'?';

        return $columnNameOnly.' IN ('.$inParameters.')';
    }

    /**
     * Insert or update entity in the storage.
     *
     * @param int $identifier
     * @param array $data
     *
     * @return int The ID of the saved entity in the storage
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function saveData(int $identifier, array $data) : int
    {
        if (empty($identifier)) {
            $query = "INSERT INTO {$this->dataGroup}";
        } else {
            $query = "UPDATE {$this->dataGroup}";
        }

        $queryData = [];
        $queryBind = [];

        foreach ($data as $fieldName => $value) {
            $queryData[] = "{$fieldName} = ?";
            $queryBind[] = $value;
        }

        $query .= ' SET '.implode(', ', $queryData);

        if (!empty($identifier)) {
            $query .= " WHERE {$this->idKey} = ?";
            $queryBind[] = $identifier;
        }

        $statement = $this->dataDriver->prepare($query);
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return empty($identifier) ? (int)$this->dataDriver->lastInsertId() : $identifier;
    }

    /**
     * Binds values to the statement.
     *
     * @param PDOStatement $statement
     * @param array        $queryBind
     * @return void
     *
     * @codeCoverageIgnore Don't test external library.
     */
    private function bindValuesToStatement(PDOStatement&$statement, array $queryBind) : void
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
     * @param int $identifier
     * @return bool
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function deleteData(int $identifier) : bool
    {
        $statement = $this->dataDriver->prepare("DELETE FROM WHERE {$this->idKey} = ?");

        return $statement->execute([$identifier]);
    }
}
