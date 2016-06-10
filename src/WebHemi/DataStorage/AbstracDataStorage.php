<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\DataStorage;

use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\DataEntity\DataEntityInterface;

/**
 * Class AbstractDataStorage.
 * Suppose to hide DataAdapter and DataEntity instances from children Storage objects.
 * @package WebHemi\DataStorage
 */
abstract class AbstractDataStorage implements DataStorageInterface
{
    /** @var DataAdapterInterface  */
    private $defaultAdapter;
    /** @var DataEntityInterface  */
    private $entityPrototype;
    /** @var string  */
    protected $dataGroup;
    /** @var  string */
    protected $idKey;

    /**
     * UserMetaStorage constructor. The DataEntity SHOULD not be used directly unless it is required to represent
     * the same instance all the time.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface $entityPrototype
     */
    final public function __construct(DataAdapterInterface $defaultAdapter, DataEntityInterface $entityPrototype)
    {
        // Every Storage object MUST have unique adapter instance to avoid override private properties like "dataGroup"
        $this->defaultAdapter = clone $defaultAdapter;

        if (!empty($this->dataGroup)) {
            $this->defaultAdapter->setDataGroup($this->dataGroup);
        }

        if (!empty($this->idKey)) {
            $this->defaultAdapter->setIdKey($this->idKey);
        }

        $this->entityPrototype = $entityPrototype;
        $this->init();
    }

    /**
     * Special initialization method. The constructor MUST call it.
     *
     * @return DataStorageInterface
     */
    public function init()
    {
        return $this;
    }

    /**
     * Returns the DataAdapter instance
     *
     * @return DataAdapterInterface
     */
    final public function getDataAdapter()
    {
        return $this->defaultAdapter;
    }

    /**
     * Creates an empty entity. Should be use by getters.
     *
     * @return DataEntityInterface
     */
    final public function createEntity()
    {
        return clone $this->entityPrototype;
    }
}
