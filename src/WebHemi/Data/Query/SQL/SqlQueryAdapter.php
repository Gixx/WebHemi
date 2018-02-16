<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Query\SQL;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use WebHemi\Data\Driver\DriverInterface;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class SqlQueryAdapter
 */
class SqlQueryAdapter implements QueryInterface
{
    /**
     * @var PDO
     */
    private $driverAdapter;

    /**
     * @var array
     */
    private $identifierList = [];

    /**
     * QueryInterface constructor.
     *
     * @param DriverInterface $driverAdapter
     */
    public function __construct(DriverInterface $driverAdapter)
    {
        $this->driverAdapter = $driverAdapter;
        $this->init();
    }

    /**
     * Collects all the valid statements.
     */
    private function init() : void
    {
        $statementFiles = glob(__DIR__.'/statements/*.sql', GLOB_BRACE);

        foreach ($statementFiles as $file) {
            $this->identifierList[basename($file, '.sql')] = $file;
        }
    }

    /**
     * Returns the Data Driver instance.
     *
     * @return DriverInterface
     */
    public function getDriver() : DriverInterface
    {
        return $this->driverAdapter;
    }

    /**
     * Returns all the query identifiers assigned to the query adapter.
     *
     * @return array
     */
    public function getQueryIdentifierList() : array
    {
        return $this->identifierList;
    }

    /**
     * Fetches data buy executing a query identified by ID.
     *
     * @param string $queryIdentifier
     * @param array $parameters
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @return null|array
     */
    public function fetchData(string $queryIdentifier, array $parameters = []) : ? array
    {
        $data = null;

        if (!isset($this->identifierList[$queryIdentifier])) {
            throw new InvalidArgumentException(
                sprintf('No such query found for this adapter: "%s"', $queryIdentifier),
                1000
            );
        }

        $query = file_get_contents($this->identifierList[$queryIdentifier]);

        $statement = $this->driverAdapter->prepare($query);

        foreach ($parameters as $parameter => $value) {
            $statement->bindValue($parameter, $value, $this->getValueType($value));
        }

        try {
            $executedSuccessful = $statement->execute();
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                sprintf('Error executing query for "%s". %s', $queryIdentifier, $exception->getMessage()),
                1000
            );
        }

        if (!$executedSuccessful) {
            throw new RuntimeException(
                sprintf('Error running query: "%s"', $queryIdentifier),
                1001
            );
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($result)) {
            $data = $result;
        }

        return $data;
    }

    /**
     * Returns the PDO type of the value.
     *
     * @param mixed $value
     * @return int
     */
    private function getValueType($value) : int
    {
        $type = PDO::PARAM_STR;

        if (is_numeric($value)) {
            $type = PDO::PARAM_INT;
        } elseif (is_null($value)) {
            $type = PDO::PARAM_NULL;
        } elseif (is_bool($value)) {
            $type = PDO::PARAM_BOOL;
        }

        return $type;
    }
}
