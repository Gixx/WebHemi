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

namespace WebHemi\Data\Query;

use InvalidArgumentException;
use WebHemi\Data\Driver\DriverInterface;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    const MAX_ROW_LIMIT = 1500;

    /**
     * QueryInterface constructor.
     *
     * @param DriverInterface $driverAdapter
     */
    public function __construct(DriverInterface $driverAdapter);

    /**
     * Returns the Data Driver instance.
     *
     * @return DriverInterface
     */
    public function getDriver() : DriverInterface;

    /**
     * Returns all the query identifiers assigned to the query adapter.
     *
     * @return array
     */
    public function getQueryIdentifierList() : array;

    /**
     * Fetches data buy executing a query identified by ID.
     *
     * @param string $queryIdentifier
     * @param array $parameters
     * @throws InvalidArgumentException
     * @return null|array
     */
    public function fetchData(string $queryIdentifier, array $parameters = []) : ? array;
}
