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
 * Class AbstractDataStorage.
 * Suppose to hide DataAdapter and DataEntity instances from children Storage objects.
 */
abstract class AbstractDataStorage implements DataStorageInterface
{
    /** @var DataAdapterInterface */
    private $defaultAdapter;
    /** @var DataEntityInterface */
    private $entityPrototype;
    /** @var string */
    protected $dataGroup;
    /** @var string */
    protected $idKey;
    /** @var bool */
    protected $initialized = false;

    /**
     * UserMetaStorage constructor. The DataEntity SHOULD not be used directly unless it is required to represent
     * the same instance all the time.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface  $entityPrototype
     */
    final public function __construct(DataAdapterInterface $defaultAdapter, DataEntityInterface $entityPrototype)
    {
        // Every Storage object MUST have unique adapter instance to avoid override private properties like "dataGroup"
        $this->defaultAdapter = clone $defaultAdapter;
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
        // They always walk in pair.
        if (!empty($this->dataGroup) && !empty($this->idKey)) {
            $this->defaultAdapter->setDataGroup($this->dataGroup);
            $this->defaultAdapter->setIdKey($this->idKey);

            $this->initialized = true;
        }

        return $this;
    }

    /**
     * Checks if the storage is initialized.
     *
     * @return bool
     */
    final public function initialized()
    {
        return $this->initialized;
    }

    /**
     * Returns the DataAdapter instance.
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

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    abstract protected function populateEntity(DataEntityInterface &$entity, array $data);
}
