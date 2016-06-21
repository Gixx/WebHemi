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
namespace WebHemi\DataStorage;

use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\DataEntity\DataEntityInterface;

/**
 * Interface DataStorageInterface.
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
     * Returns the DataAdapter instance.
     *
     * @return DataAdapterInterface
     */
    public function getDataAdapter();
}
