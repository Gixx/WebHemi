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
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * StorageInterface constructor.
     *
     * @param QueryInterface $queryAdapter
     * @param EntitySet $entitySetPrototype
     * @param EntityInterface[] ...$entityPrototypes
     */
    public function __construct(
        QueryInterface $queryAdapter,
        EntitySet $entitySetPrototype,
        EntityInterface ...$entityPrototypes
    );

    /**
     * @return QueryInterface
     */
    public function getQueryAdapter() : QueryInterface;

    /**
     * Creates a clean instance of the Entity.
     *
     * @param string $entityClass
     * @throws InvalidArgumentException
     * @return EntityInterface
     */
    public function createEntity(string $entityClass) : EntityInterface;

    /**
     * Creates an empty entity set.
     *
     * @return EntitySet
     */
    public function createEntitySet() : EntitySet;
}
