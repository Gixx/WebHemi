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

namespace WebHemi\Data\Coupler;

use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Interface DataCouplerInterface.
 *
 * A Data Coupler represents an N:N connection for entities.
 */
interface DataCouplerInterface
{
    /**
     * DataCouplerInterface constructor.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface  $dataEntityPrototypeA
     * @param DataEntityInterface  $dataEntityPrototypeB
     */
    public function __construct(
        DataAdapterInterface $defaultAdapter,
        DataEntityInterface $dataEntityPrototypeA,
        DataEntityInterface $dataEntityPrototypeB
    );

    /**
     * Returns the DataAdapter instance.
     *
     * @return DataAdapterInterface
     */
    public function getDataAdapter() : DataAdapterInterface;

    /**
     * Gets all the entities those are depending from the given entity.
     *
     * @param DataEntityInterface $entity
     * @return array<DataEntityInterface>
     */
    public function getEntityDependencies(DataEntityInterface $entity) : array;

    /**
     * Sets dependency for the entities
     *
     * @param DataEntityInterface $entityA
     * @param DataEntityInterface $entityB
     * @return int The ID of the saved entity in the storage
     */
    public function setDependency(DataEntityInterface $entityA, DataEntityInterface $entityB) : int;
}
