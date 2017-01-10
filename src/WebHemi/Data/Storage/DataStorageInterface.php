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
namespace WebHemi\Data\Storage;

use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Interface DataStorageInterface.
 *
 * A Data Storage is the connection between the Data Adapter and the Data Entity. It uses the Adapter to get/set the
 * data and populates into/from the Entity.
 */
interface DataStorageInterface
{
    /**
     * DataStorageInterface constructor. The DataEntity SHOULD not be used directly unless it is required to represent
     * the same instance all the time.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface  $entityPrototype
     */
    public function __construct(DataAdapterInterface $defaultAdapter, DataEntityInterface $entityPrototype);

    /**
     * Special initialization method. The constructor MUST call it.
     *
     * @return DataStorageInterface
     */
    public function init();

    /**
     * Checks if the storage is initialized.
     *
     * @return bool
     */
    public function initialized();

    /**
     * Creates an empty entity.
     *
     * @return DataEntityInterface
     */
    public function createEntity();

    /**
     * Saves data.
     *
     * @param DataEntityInterface &$entity
     * @return DataStorageInterface
     */
    public function saveEntity(DataEntityInterface&$entity);

    /**
     * Returns the DataAdapter instance.
     *
     * @return DataAdapterInterface
     */
    public function getDataAdapter();
}
