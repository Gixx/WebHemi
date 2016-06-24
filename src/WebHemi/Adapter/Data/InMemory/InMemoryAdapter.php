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
namespace WebHemi\Adapter\Data\InMemory;

use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Adapter\Exception\InitException;
use WebHemi\Adapter\Exception\InvalidArgumentException;

/**
 * Class InMemoryAdapter.
 */
class InMemoryAdapter implements DataAdapterInterface
{
    const EXPRESSION_IN_ARRAY = 'IN';
    const EXPRESSION_WILDCARD = 'LIKE';

    /** @var array */
    private $dataStorage;
    /** @var string */
    private $dataGroup = 'default';
    /** @var string */
    private $idKey = 'id';

    /**
     * PDOAdapter constructor.
     *
     * @param mixed $dataStorage
     *
     * @throws InvalidArgumentException
     */
    public function __construct($dataStorage = null)
    {
        if (empty($dataStorage)) {
            $dataStorage = [];
        }

        if (!is_array($dataStorage)) {
            throw new InvalidArgumentException('The constructor parameter must be empty or an array.');
        }

        foreach ($dataStorage as $rowData) {
            if (!is_array($rowData)) {
                throw new InvalidArgumentException('The constructor parameter if present must be an array of arrays.');
            }
        }

        $this->dataStorage[$this->dataGroup] = $dataStorage;
    }

    /**
     * Returns the Data Storage instance.
     *
     * @return array
     */
    public function getDataStorage()
    {
        return $this->dataStorage;
    }

    /**
     * Set adapter data group.
     *
     * @param string $dataGroup
     *
     * @throws InitException
     *
     * @return InMemoryAdapter
     */
    public function setDataGroup($dataGroup)
    {
        // Allow to change only once.
        if ($this->dataGroup !== 'default') {
            throw new InitException('Can\'t re-initialize dataGroup property. Property is already set.');
        }

        $this->dataGroup = $dataGroup;

        // Copy all previous init data.
        $this->dataStorage[$dataGroup] = $this->dataStorage['default'];
        unset($this->dataStorage['default']);

        return $this;
    }

    /**
     * Set adapter ID key. For Databases this can be the Primary key. Only simple key is allowed.
     *
     * @param string $idKey
     *
     * @throws InitException
     *
     * @return $this
     */
    public function setIdKey($idKey)
    {
        // Allow to change only once.
        if ($this->idKey !== 'id') {
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
        $result = [];

        $dataStorage = $this->getDataStorage()[$this->dataGroup];

        if (isset($dataStorage[$identifier])) {
            $result = $dataStorage[$identifier];
        }

        return $result;
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
        $result = [];

        $dataStorage = $this->getDataStorage()[$this->dataGroup];
        $limitCounter = 0;
        $offsetCounter = 0;

        foreach ($dataStorage as $data) {
            if ($this->isExpressionMatch($expression, $data)) {
                if (!is_null($limit) && $limitCounter >= $limit) {
                    break;
                }

                if (!is_null($offset) && $offsetCounter++ < $offset) {
                    continue;
                }

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
     *
     * @return bool
     */
    private function isExpressionMatch(array $expression, array $data)
    {
        $match = true;

        foreach ($expression as $pattern => $subject) {
            $dataKey = '';
            $expressionType = $this->getExpressionType($pattern, $dataKey);

            if ($expressionType == self::EXPRESSION_WILDCARD) {
                $match = $this->checkWildcardMatch($data[$dataKey], $subject);
            } elseif ($expressionType == self::EXPRESSION_IN_ARRAY) {
                $match = $this->checkInArrayMatch($data[$dataKey], $subject);
            } else {
                $match = $this->checkRelation($expressionType, $data[$dataKey], $subject);
            }

            // First false means some expression is failing for the data row, so the whole expression set is failing.
            if (!$match) {
                break;
            }
        }

        return (bool)$match;
    }

    /**
     * @param mixed $data
     * @param mixed $subject
     *
     * @return bool
     */
    private function checkWildcardMatch($data, $subject)
    {
        $subject = str_replace('%', '.*', $subject);
        return preg_match('/^' . $subject . '$/', $data);
    }

    /**
     * @param mixed $data
     * @param mixed $subject
     *
     * @return bool
     */
    private function checkInArrayMatch($data, $subject)
    {
        return in_array($data, (array)$subject);
    }

    /**
     * @param string $relation
     * @param mixed  $data
     * @param mixed  $subject
     *
     * @return bool
     */
    private function checkRelation($relation, $data, $subject)
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
     *
     * @return string
     */
    private function getExpressionType($pattern, &$subject)
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
     *
     * @return int
     */
    public function getDataCardinality(array $expression)
    {
        $list = $this->getDataSet($expression);

        return count($list);
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
        $dataStorage = $this->getDataStorage()[$this->dataGroup];

        if (empty($dataStorage) && empty($identifier)) {
            $identifier = 1;
        }

        if (empty($identifier)) {
            $keys = array_keys($dataStorage);
            $maxKey = array_pop($keys);

            if (is_numeric($maxKey)) {
                $identifier = (int) $maxKey + 1;
            } else {
                $identifier = $maxKey . '_1';
            }
        }

        // To make it sure, we always apply changes on the exact property.
        $this->dataStorage[$this->dataGroup][$identifier] = $data;

        return $identifier;
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
        $result = false;
        $dataFound = $this->getData($identifier);
        if (!empty($dataFound)) {
            unset($this->dataStorage[$this->dataGroup][$identifier]);
            $result = true;
        }

        return $result;
    }
}
