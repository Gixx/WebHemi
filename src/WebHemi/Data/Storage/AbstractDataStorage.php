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
namespace WebHemi\Data\Storage;

use InvalidArgumentException;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\DataEntityInterface;

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
     * AbstractDataStorage constructor. The DataEntity SHOULD not be used directly unless it is required to represent
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
     * Saves data.
     *
     * @param DataEntityInterface &$entity
     * @return mixed The ID of the saved entity in the storage
     */
    public function saveEntity(DataEntityInterface &$entity)
    {
        $entityClass = get_class($entity);
        $storageEntityClass = get_class($this->entityPrototype);

        if ($entityClass != $storageEntityClass) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use %s with this data storage class. You must use %s.',
                    $entityClass,
                    $storageEntityClass
                )
            );
        }

        $dataId = $this->getDataAdapter()->saveData($entity->getKeyData(), $this->getEntityData($entity));

        // If key data is empty, then it was an insert. Get a new entity with all data.
        if ($dataId && empty($entity->getKeyData())) {
            $entityData = $this->getDataAdapter()->getData($dataId);
            $this->populateEntity($entity, $entityData);
        }

        return $dataId;
    }

    /**
     * Get data from an entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    abstract protected function getEntityData(DataEntityInterface $entity);

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    abstract protected function populateEntity(DataEntityInterface&$entity, array $data);
}
