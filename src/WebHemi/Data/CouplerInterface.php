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

namespace WebHemi\Data;

/**
 * Interface CouplerInterface.
 *
 * A Data Coupler represents an N:N connection for entities.
 */
interface CouplerInterface
{
    /**
     * CouplerInterface constructor.
     *
     * @param ConnectorInterface $dataAdapter
     * @param EntityInterface    $dataEntityPrototypeA
     * @param EntityInterface    $dataEntityPrototypeB
     */
    public function __construct(
        ConnectorInterface $dataAdapter,
        EntityInterface $dataEntityPrototypeA,
        EntityInterface $dataEntityPrototypeB
    );

    /**
     * Returns the Connector instance.
     *
     * @return ConnectorInterface
     */
    public function getConnector() : ConnectorInterface;

    /**
     * Gets all the entities those are depending from the given entity.
     *
     * @param  EntityInterface $dataEntity
     * @return EntityInterface[]
     */
    public function getEntityDependencies(EntityInterface $dataEntity) : array;

    /**
     * Sets dependency for the entities
     *
     * @param  EntityInterface $dataEntityA
     * @param  EntityInterface $dataEntityB
     * @return int The ID of the saved entity in the storage
     */
    public function setDependency(EntityInterface $dataEntityA, EntityInterface $dataEntityB) : int;
}
