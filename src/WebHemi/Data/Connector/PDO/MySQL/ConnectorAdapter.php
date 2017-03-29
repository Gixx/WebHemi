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

namespace WebHemi\Data\Connector\PDO\MySQL;

use InvalidArgumentException;
use PDO;
use PDOStatement;
use RuntimeException;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\DriverInterface;

/**
 * Class ConnectorAdapter.
 */
class ConnectorAdapter implements ConnectorInterface
{
    /** @var PDO */
    protected $dataDriver;
    /** @var string */
    protected $dataGroup = null;
    /** @var string */
    protected $idKey = null;

    /**
     * ConnectorAdapter constructor.
     *
     * @param DriverInterface $dataDriver
     * @throws InvalidArgumentException
     */
    public function __construct(DriverInterface $dataDriver)
    {
        if (!$dataDriver instanceof DriverAdapter) {
            $type = gettype($dataDriver);

            if ($type == 'object') {
                $type = get_class($dataDriver);
            }

            $message = sprintf(
                'Can\'t create %s instance. The parameter must be an instance of MySQLDriver, %s given.',
                __CLASS__,
                $type
            );

            throw new InvalidArgumentException($message, 1001);
        }

        $this->dataDriver = $dataDriver;
    }

    /**
     * Returns the Data Storage instance.
     *
     * @return DriverInterface
     */
    public function getDataDriver() : DriverInterface
    {
        return $this->dataDriver;
    }

    /**
     * Set adapter data group. For Databases this can be the Tables.
     *
     * @param string $dataGroup
     * @return ConnectorInterface
     */
    public function setDataGroup(string $dataGroup) : ConnectorInterface
    {
        $this->dataGroup = $dataGroup;

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @return ConnectorInterface
     */
    public function setIdKey(string $idKey) : ConnectorInterface
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
        $queryBinds = [];

        $query = $this->getSelectQueryForExpression(
            [$this->idKey => $identifier],
            $queryBinds,
            [self::OPTION_LIMIT => 1, self::OPTION_OFFSET => 0]
        );
        $statement = $this->dataDriver->prepare($query);
        $this->bindValuesToStatement($statement, $queryBinds);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        return $data ? $data : [];
    }

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param array $options
     * @return array
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function getDataSet(array $expression, array $options = []) : array
    {
        $queryBinds = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBinds, $options);
        $statement = $this->dataDriver->prepare($query);

        $this->bindValuesToStatement($statement, $queryBinds);
        $statement->execute();

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $data ? $data : [];
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
        $queryBinds = [];

        $query = $this->getSelectQueryForExpression($expression, $queryBinds, []);
        $statement = $this->dataDriver->prepare($query);
        $this->bindValuesToStatement($statement, $queryBinds);
        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * Builds SQL query from the expression.
     *
     * @param array $expression
     * @param array $queryBinds
     * @param array $options
     * @return string
     */
    protected function getSelectQueryForExpression(
        array $expression,
        array&$queryBinds,
        array $options = []
    ) : string {
        $query = "SELECT * FROM {$this->dataGroup}";

        // Prepare WHERE expression.
        if (!empty($expression)) {
            $query .= $this->getWhereExpression($expression, $queryBinds);
        }

        $group = $this->getQueryGroup($options);
        $having = $this->getQueryHaving($options);

        if (!empty($group)) {
            $query .= " GROUP BY {$group}";

            if (!empty($having)) {
                $query .= " HAVING {$having}";
            }
        }

        $query .= " ORDER BY {$this->getQueryOrder($options)}";

        $limit = $this->getQueryLimit($options);

        if ($limit > 0) {
            $query .= " LIMIT {$limit}";
            $query .= " OFFSET {$this->getQueryOffset($options)}";
        }

        return $query;
    }

    /**
     * Gets the GROUP BY expression.
     *
     * @param array $options
     * @return string
     */
    protected function getQueryGroup(array $options) : string
    {
        return $options[self::OPTION_GROUP] ?? '';
    }

    /**
     * Gets the HAVING expression only when the GROUP BY option exists.
     *
     * @param array $options
     * @return string
     */
    protected function getQueryHaving(array $options) : string
    {
        return isset($options[self::OPTION_GROUP]) ? $options[self::OPTION_HAVING] : '';
    }

    /**
     * Gets the ORDER BY expression. The default value is the primary key.
     *
     * @param array $options
     * @return string
     */
    protected function getQueryOrder(array $options) : string
    {
        return $options[self::OPTION_ORDER] ?? $this->idKey;
    }

    /**
     * Gets the LIMIT expression.
     *
     * @param array $options
     * @return int
     */
    protected function getQueryLimit(array $options) : int
    {
        return $options[self::OPTION_LIMIT] ?? 0;
    }

    /**
     * Gets the OFFSET expression.
     *
     * @param array $options
     * @return int
     */
    protected function getQueryOffset(array $options) : int
    {
        return $options[self::OPTION_OFFSET] ?? 0;
    }

    /**
     * Creates a WHERE expression for the SQL query.
     *
     * @param array $expression
     * @param array $queryBinds
     * @return string
     */
    protected function getWhereExpression(array $expression, array&$queryBinds) : string
    {
        $whereExpression = '';
        $queryParams = [];

        foreach ($expression as $column => $value) {
            $this->setParamsAndBinds($column, $value, $queryParams, $queryBinds);
        }

        if (!empty($queryParams)) {
            $whereExpression = ' WHERE '.implode(' AND ', $queryParams);
        }

        return $whereExpression;
    }

    /**
     * Set the query params and quaery bindings according to the `column` and `value`.
     *
     * @param string $column
     * @param mixed  $value
     * @param array  $queryParams
     * @param array  $queryBinds
     */
    protected function setParamsAndBinds(string $column, $value, array&$queryParams, array&$queryBinds) : void
    {
        if (is_array($value)) {
            $queryParams[] = $this->getInColumnCondition($column, count($value));
            $queryBinds = array_merge($queryBinds, $value);
        } elseif (strpos($column, ' LIKE') !== false || (is_string($value) && strpos($value, '%') !== false)) {
            $queryParams[] = $this->getLikeColumnCondition($column);
            $queryBinds[] = $value;
        } else {
            $queryParams[] = $this->getSimpleColumnCondition($column);
            $queryBinds[] = $value;
        }
    }

    /**
     * Gets a simple condition for the column.
     *
     * @param string $column
     * @return string 'my_column = ?'
     */
    protected function getSimpleColumnCondition(string $column) : string
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
    protected function getLikeColumnCondition(string $column) : string
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
    protected function getInColumnCondition(string $column, int $parameterCount = 1) : string
    {
        list($columnNameOnly) = explode(' ', $column);

        $inParameters = str_repeat('?,', $parameterCount - 1).'?';

        return $columnNameOnly.' IN ('.$inParameters.')';
    }

    /**
     * Insert or update entity in the storage.
     *
     * @param int   $identifier
     * @param array $data
     * @throws RuntimeException
     * @return int The ID of the saved entity in the storage
     *
     * @codeCoverageIgnore Don't test external library.
     */
    public function saveData(? int $identifier = null, array $data) : int
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
        if (!$statement) {
            throw new RuntimeException('Query error', 1002);
        }
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return empty($identifier) ? (int) $this->dataDriver->lastInsertId() : $identifier;
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
    protected function bindValuesToStatement(PDOStatement&$statement, array $queryBind) : void
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
