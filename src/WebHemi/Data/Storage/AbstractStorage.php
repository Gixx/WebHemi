<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Storage;

use InvalidArgumentException;
use WebHemi\Data\Query\QueryInterface;
use WebHemi\Data\Entity\EntityInterface;
use WebHemi\Data\Entity\EntitySet;

/**
 * Class AbstractStorage.
 * Suppose to hide Data Service Adapter and Data Entity instances from children Storage objects.
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var QueryInterface
     */
    private $queryAdapter;

    /**
     * @var EntitySet
     */
    private $entitySetPrototype;

    /**
     * @var EntityInterface[]
     */
    private $entityPrototypes;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * AbstractStorage constructor.
     *
     * @param QueryInterface $queryAdapter
     * @param EntitySet $entitySetPrototype
     * @param EntityInterface ...$entityPrototypes
     */
    public function __construct(
        QueryInterface $queryAdapter,
        EntitySet $entitySetPrototype,
        EntityInterface ...$entityPrototypes
    ) {
        $this->queryAdapter = $queryAdapter;
        $this->entitySetPrototype = $entitySetPrototype;

        foreach ($entityPrototypes as $entity) {
            $this->entityPrototypes[\get_class($entity)] = $entity;
        }
    }

    /**
     * @return QueryInterface
     */
    public function getQueryAdapter() : QueryInterface
    {
        return $this->queryAdapter;
    }

    /**
     * Creates a clean instance of the Entity.
     *
     * @param string $entityClass
     * @throws InvalidArgumentException
     * @return EntityInterface
     */
    public function createEntity(string $entityClass) : EntityInterface
    {
        if (!isset($this->entityPrototypes[$entityClass])) {
            throw new InvalidArgumentException(
                sprintf('Entity class reference "%s" is not defined in this class.', $entityClass),
                1000
            );
        }

        return clone $this->entityPrototypes[$entityClass];
    }

    /**
     * Get an entity instance with data.
     *
     * @param string $entityClass
     * @param array  $data
     * @throws InvalidArgumentException
     * @return null|EntityInterface
     */
    protected function getEntity(string $entityClass, array $data) : ? EntityInterface
    {
        if (!empty($data)) {
            $entity = $this->createEntity($entityClass);
            $entity->fromArray($data);
            return $entity;
        }

        return null;
    }

    /**
     * Creates an empty entity set.
     *
     * @return EntitySet
     */
    public function createEntitySet() : EntitySet
    {
        return clone $this->entitySetPrototype;
    }

    /**
     * Creates and fills and EntitySet
     *
     * @param string $entityClass
     * @param array|null $data
     * @throws InvalidArgumentException
     * @return EntitySet
     */
    protected function getEntitySet(string $entityClass, ? array $data) : EntitySet
    {
        $entitySet = $this->createEntitySet();

        if ($data === null) {
            $data = [];
        }

        foreach ($data as $row) {
            $entity = $this->getEntity($entityClass, $row);

            if ($entity !== null) {
                $entitySet[] = $entity;
            }
        }

        return $entitySet;
    }

    /**
     * Checks and corrects values to stay within the limits.
     *
     * @param int $limit
     * @param int $offset
     */
    protected function normalizeLimitAndOffset(int&$limit, int&$offset) : void
    {
        $limit = min(QueryInterface::MAX_ROW_LIMIT, abs($limit));
        $offset = (int) abs($offset);
    }
}
