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

namespace WebHemi\Data\Storage;

use WebHemi\Data\Query\QueryInterface;

/**
 * Class AbstractStorage.
 * Suppose to hide Data Service Adapter and Data Entity instances from children Storage objects.
 */
abstract class AbstractStorage
{
    /**
     * @var QueryInterface
     */
    protected $queryAdapter;
    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * AbstractStorage constructor.
     *
     * @param QueryInterface $queryAdapter
     */
    public function __construct(QueryInterface $queryAdapter)
    {
        $this->queryAdapter = $queryAdapter;
    }

    /**
     * @return QueryInterface
     */
    public function getQueryAdapter() : QueryInterface
    {
        return $this->queryAdapter;
    }

    /**
     * Checks and corrects values to stay within the limits.
     *
     * @param int $limit
     * @param int $offset
     */
    protected function normalizeLimitAndOffset(int&$limit, int&$offset)
    {
        $limit = min(QueryInterface::MAX_ROW_LIMIT, abs($limit));
        $offset = abs($offset);
    }
}
