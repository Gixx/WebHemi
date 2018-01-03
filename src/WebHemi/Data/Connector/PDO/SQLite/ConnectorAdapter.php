<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Connector\PDO\SQLite;

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Data\DriverInterface;
use WebHemi\Data\Connector\PDO\MySQL\ConnectorAdapter as MySQLAdapter;

/**
 * Class ConnectorAdapter.
 *
 * @codeCoverageIgnore - Used by unit test only.
 */
class ConnectorAdapter extends MySQLAdapter
{
    /**
     * ConnectorAdapter constructor.
     *
     * @param string          $name
     * @param DriverInterface $dataDriver
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, DriverInterface $dataDriver)
    {
        if (!$dataDriver instanceof DriverAdapter) {
            $type = gettype($dataDriver);

            if ($type == 'object') {
                $type = get_class($dataDriver);
            }

            $message = sprintf(
                'Can\'t create %s instance. The parameter must be an instance of SQLiteDriver, %s given.',
                __CLASS__,
                $type
            );

            throw new InvalidArgumentException($message, 1001);
        }

        $this->name = $name;
        $this->dataDriver = $dataDriver;
    }

    /**
     * Insert or update entity in the storage.
     *
     * @param int   $identifier
     * @param array $data
     * @throws RuntimeException
     * @return int The ID of the saved entity in the storage
     */
    public function saveData(? int $identifier = null, array $data = []) : int
    {
        return empty($identifier) ? $this->insertData($data) : $this->updateData($identifier, $data);
    }

    /**
     * Insert data.
     *
     * @param array $data
     * @return int
     */
    protected function insertData(array $data) : int
    {
        $query = "INSERT INTO {$this->dataGroup}";

        $queryColumns = [];
        $queryData = [];
        $queryBind = [];

        foreach ($data as $fieldName => $value) {
            $queryColumns[] = $fieldName;
            $queryData[] = "?";
            $queryBind[] = $value;
        }

        $query .= ' ('.implode(', ', $queryColumns).') VALUES ('.implode(', ', $queryData).')';

        $statement = $this->dataDriver->prepare($query);

        if (!$statement) {
            throw new RuntimeException('Query error', 1002);
        }
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return (int) $this->dataDriver->lastInsertId();
    }

    /**
     * Update data.
     *
     * @param int   $identifier
     * @param array $data
     * @return int
     */
    protected function updateData(int $identifier, array $data) : int
    {
        $query = "UPDATE {$this->dataGroup}";

        $queryData = [];
        $queryBind = [];

        foreach ($data as $fieldName => $value) {
            $queryData[] = "{$fieldName} = ?";
            $queryBind[] = $value;
        }

        $query .= ' SET '.implode(', ', $queryData)." WHERE {$this->idKey} = ?";
        $queryBind[] = $identifier;
        $statement = $this->dataDriver->prepare($query);

        if (!$statement) {
            throw new RuntimeException('Query error', 1003);
        }
        $this->bindValuesToStatement($statement, $queryBind);
        $statement->execute();

        return $identifier;
    }
}
