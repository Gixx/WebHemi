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

namespace WebHemi\Adapter\Data\InMemory;

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class InMemoryAdapter.
 */
class InMemoryAdapter implements DataAdapterInterface
{
    /** @var DataDriverInterface */
    private $dataDriver;
    /** @var string */
    private $dataGroup = 'default';
    /** @var string */
    private $idKey = 'id';

    /**
     * MySQLAdapter constructor.
     *
     * @param DataDriverInterface $dataDriver
     * @throws InvalidArgumentException
     */
    public function __construct(DataDriverInterface $dataDriver)
    {
        /** @var InMemoryDriver $dataDriver */
        $dataCollection = $dataDriver->toArray();

        foreach ($dataCollection as $rowData) {
            if (!is_array($rowData)) {
                throw new InvalidArgumentException(
                    'The constructor parameter if present must be an array of arrays.',
                    1001
                );
            }
        }

        $this->dataDriver[$this->dataGroup] = $dataCollection;
    }

    /**
     * Returns the Data Driver instance.
     *
     * @return DataDriverInterface
     */
    public function getDataDriver() : DataDriverInterface
    {
        return $this->dataDriver;
    }

    /**
     * Set adapter data group.
     *
     * @param string $dataGroup
     * @throws RuntimeException
     * @return DataAdapterInterface
     */
    public function setDataGroup(string $dataGroup) : DataAdapterInterface
    {
        // Allow to change only once.
        if ($this->dataGroup !== 'default') {
            throw new RuntimeException('Can\'t re-initialize dataGroup property. Property is already set.', 1002);
        }

        $this->dataGroup = $dataGroup;

        // Copy all previous init data.
        $this->dataDriver[$dataGroup] = $this->dataDriver['default'];
        unset($this->dataDriver['default']);

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     * @throws RuntimeException
     * @return DataAdapterInterface
     */
    public function setIdKey(string $idKey) : DataAdapterInterface
    {
        // Allow to change only once.
        if ($this->idKey !== 'id') {
            throw new RuntimeException('Can\'t re-initialize idKey property. Property is already set.', 1003);
        }

        $this->idKey = $idKey;

        return $this;
    }

    /**
     * Get exactly one "row" of data according to the expression.
     *
     * @param int $identifier
     * @return array
     */
    public function getData(int $identifier) : array
    {
        $result = [];

        $dataDriver = $this->dataDriver[$this->dataGroup];

        if (isset($dataDriver[$identifier])) {
            $result = $dataDriver[$identifier];
        }

        return $result;
    }

    /**
     * Get a set of data according to the expression and the chunk.
     *
     * @param array $expression
     * @param int   $limit
     * @param int   $offset
     * @return array
     */
    public function getDataSet(array $expression, int $limit = PHP_INT_MAX, int $offset = 0) : array
    {
        $result = [];

        $dataDriver = $this->dataDriver[$this->dataGroup];
        $limitCounter = 0;
        $offsetCounter = 0;

        foreach ($dataDriver as $data) {
            $match = $this->isExpressionMatch($expression, $data);
            $offsetReached = $offsetCounter >= $offset;

            if ($limitCounter >= $limit) {
                break;
            }

            if ($match && !$offsetReached) {
                $offsetCounter++;
                continue;
            }

            if ($match) {
                $limitCounter++;
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * Checks the data (row) array against the expressions.
     *
     * @param array $expression
     * @param array $data
     * @return bool
     */
    private function isExpressionMatch(array $expression, array $data) : bool
    {
        foreach ($expression as $pattern => $subject) {
            $dataKey = '';
            $expressionType = $this->getExpressionType($pattern, $dataKey);

            // First false means some expression is failing for the data row, so the whole expression set is failing.
            if (!$this->match($expressionType, $data[$dataKey], $subject)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Matches data against the subject according to the expression type.
     *
     * @param string $expressionType
     * @param mixed  $data
     * @param string $subject
     * @return bool
     *
     * @todo handle 'NOT IN' and 'NOT LIKE' expressions too.
     */
    private function match(string $expressionType, $data, string $subject) : bool
    {
        if ($expressionType == 'LIKE') {
            $match = $this->checkWildcardMatch($data, $subject);
        } elseif ($expressionType == 'IN') {
            $match = $this->checkInArrayMatch($data, $subject);
        } else {
            $match = $this->checkRelation($expressionType, $data, $subject);
        }

        return $match;
    }

    /**
     * Checks for wildcard match.
     *
     * @param string $data
     * @param string $subject
     * @return bool
     */
    private function checkWildcardMatch(string $data, string $subject) : bool
    {
        $subject = str_replace('%', '.*', $subject);
        return (bool) preg_match('/^'.$subject.'$/', $data);
    }

    /**
     * Checks for match in array.
     *
     * @param array  $data
     * @param string $subject
     *
     * @return bool
     */
    private function checkInArrayMatch(array $data, string $subject) : bool
    {
        return in_array($data, (array) $subject);
    }

    /**
     * Checks for relation match.
     *
     * @param string $relation
     * @param string $data
     * @param string $subject
     * @return bool
     */
    private function checkRelation(string $relation, string $data, string $subject) : bool
    {
        $expressionMap = [
            '<'  => $data < $subject,
            '<=' => $data <= $subject,
            '>'  => $data > $subject,
            '>=' => $data >= $subject,
            '<>' => $data != $subject,
            '='  => $data == $subject
        ];

        return $expressionMap[$relation];
    }

    /**
     * Gets expression type and also sets the expression subject.
     *
     * @param string $pattern
     * @param string $subject
     * @return string
     */
    private function getExpressionType(string $pattern, string &$subject) : string
    {
        $type = '=';
        $subject = $pattern;

        $regularExpressions = [
            '/^(?P<subject>[^\s]+)\s+(?P<relation>(\<\>|\<=|\>=|=|\<|\>))\s+\?$/',
            '/^(?P<subject>[^\s]+)\s+(?P<relation>LIKE)\s+\?$/',
            '/^(?P<subject>[^\s]+)\s+(?P<relation>IN)\s?\(?\?\)?$/'
        ];

        foreach ($regularExpressions as $regexPattern) {
            $matches = [];

            if (preg_match($regexPattern, $subject, $matches)) {
                $type = $matches['relation'];
                $subject = $matches['subject'];
                break;
            }
        }

        return $type;
    }



    /**
     * Get the number of matched data in the set according to the expression.
     *
     * @param array $expression
     * @return int
     */
    public function getDataCardinality(array $expression) : int
    {
        $list = $this->getDataSet($expression);

        return count($list);
    }

    /**
     * Insert or update entity in the Driver.
     *
     * @param int   $identifier
     * @param array $data
     * @return int The ID of the saved entity in the Driver
     */
    public function saveData(int $identifier, array $data) : int
    {
        $dataDriver = $this->dataDriver[$this->dataGroup];

        if (empty($dataDriver) && empty($identifier)) {
            $identifier = 1;
        }

        if (empty($identifier)) {
            $keys = array_keys($dataDriver);
            $maxKey = array_pop($keys);

            if (is_numeric($maxKey)) {
                $identifier = (int) $maxKey + 1;
            }
        }

        // To make it sure, we always apply changes on the exact property.
        $this->dataDriver[$this->dataGroup][$identifier] = $data;

        return $identifier;
    }

    /**
     * Removes an entity from the Driver.
     *
     * @param int $identifier
     * @return bool
     */
    public function deleteData(int $identifier) : bool
    {
        $result = false;
        $dataFound = $this->getData($identifier);
        if (!empty($dataFound)) {
            unset($this->dataDriver[$this->dataGroup][$identifier]);
            $result = true;
        }

        return $result;
    }
}
