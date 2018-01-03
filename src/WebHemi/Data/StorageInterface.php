<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data;

/**
 * Interface StorageInterface.
 *
 * A Data Storage is the connection between the Data Adapter and the Data Entity. It uses the Adapter to get/set the
 * data and populates into/from the Entity.
 */
interface StorageInterface
{
    /**
     * DataStorageInterface constructor. The DataEntity SHOULD not be used directly unless it is required to represent
     * the same instance all the time.
     *
     * @param ConnectorInterface $connector
     * @param EntityInterface    $dataEntityPrototype
     */
    public function __construct(ConnectorInterface $connector, EntityInterface $dataEntityPrototype);

    /**
     * Special initialization method. The constructor MUST call it.
     *
     * @return StorageInterface
     */
    public function init() : StorageInterface;

    /**
     * Checks if the storage is initialized.
     *
     * @return bool
     */
    public function initialized() : bool;

    /**
     * Creates an empty entity.
     *
     * @return EntityInterface
     */
    public function createEntity() : EntityInterface;

    /**
     * Saves data.
     *
     * @param EntityInterface &$dataEntity
     * @return StorageInterface
     */
    public function saveEntity(EntityInterface&$dataEntity) : StorageInterface;

    /**
     * Returns the Data Connector instance.
     *
     * @return ConnectorInterface
     */
    public function getConnector() : ConnectorInterface;
}
