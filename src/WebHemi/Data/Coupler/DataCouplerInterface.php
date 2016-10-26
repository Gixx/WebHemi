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
    public function getDataAdapter();

    /**
     * Gets all the entities those are depending from the given entity.
     *
     * @param DataEntityInterface $entity
     * @return array<DataEntityInterface>
     */
    public function getEntityDependencies(DataEntityInterface $entity);
}
