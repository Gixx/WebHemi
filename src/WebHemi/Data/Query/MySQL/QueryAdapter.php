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

namespace WebHemi\Data\Query\MySQL;

use InvalidArgumentException;
use PDO;
use RuntimeException;
use WebHemi\Data\Driver\DriverInterface;
use WebHemi\Data\Driver\PDO\MySQL\DriverAdapter as SQLDriverAdapter;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class QueryAdapter
 */
class QueryAdapter implements QueryInterface
{
    /**
     * @var DriverInterface
     */
    protected $driverAdapter;

    /**
     * @var array
     */
    protected $identifierList;

    /**
     * @var string
     */
    protected static $statementPath = __DIR__.'/../MySQL/statements/*.sql';

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
    protected function init() : void
    {
        $this->identifierList = [];
        $statementFiles = glob(static::$statementPath, GLOB_BRACE);

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
     * Fetches data buy executing a query identified by ID.
     *
     * @param string $query
     * @param array $parameters
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @return array
     */
    public function fetchData(string $query, array $parameters = []) : array
    {
        if (isset($this->identifierList[$query])) {
            $query = file_get_contents($this->identifierList[$query]);
        }

        // This nasty trick helps us to be able to parameterize the ORDER BY statement.
        if (isset($parameters[':orderBy'])) {
            $orderBy = $parameters[':orderBy'];
            unset($parameters[':orderBy']);

            $query = str_replace(':orderBy', $orderBy, $query);
        }

        /** @var SQLDriverAdapter $driver */
        $driver = $this->getDriver();
        $statement = $driver->prepare($query);

        foreach ($parameters as $parameter => $value) {
            $statement->bindValue($parameter, $value, $this->getValueType($value));
        }

        try {
            $executedSuccessful = $statement->execute();
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                sprintf('Error executing query for "%s". %s', $query, $exception->getMessage()),
                1000
            );
        }

        if (!$executedSuccessful) {
            throw new RuntimeException(
                sprintf('Error running query: "%s"', $query),
                1001
            );
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the PDO type of the value.
     *
     * @param mixed $value
     * @return int
     */
    protected function getValueType($value) : int
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
