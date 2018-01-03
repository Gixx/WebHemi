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

namespace WebHemi\Data\Storage;

use InvalidArgumentException;
use WebHemi\Data\EntityInterface as DataEntityInterface;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\StorageInterface;

/**
 * Class AbstractStorage.
 * Suppose to hide Data Service Adapter and Data Entity instances from children Storage objects.
 */
abstract class AbstractStorage implements StorageInterface
{
    /** @var ConnectorInterface */
    protected $connector;
    /** @var DataEntityInterface */
    private $dataEntityPrototype;
    /** @var string */
    protected $dataGroup;
    /** @var string */
    protected $idKey;
    /** @var bool */
    protected $initialized = false;

    /**
     * AbstractStorage constructor. The DataEntity SHOULD not be used directly unless it is required to represent
     * the same instance all the time.
     *
     * @param ConnectorInterface $connector
     * @param DataEntityInterface  $dataEntityPrototype
     */
    public function __construct(ConnectorInterface $connector, DataEntityInterface $dataEntityPrototype)
    {
        // Every Storage object MUST have unique adapter instance to avoid override private properties like "dataGroup"
        $this->connector = clone $connector;
        $this->dataEntityPrototype = $dataEntityPrototype;
        $this->init();
    }

    /**
     * Special initialization method. The constructor MUST call it.
     *
     * @return StorageInterface
     */
    public function init() : StorageInterface
    {
        // They always walk in pair.
        if (!empty($this->dataGroup) && !empty($this->idKey)) {
            $this->connector->setDataGroup($this->dataGroup);
            $this->connector->setIdKey($this->idKey);

            $this->initialized = true;
        }

        return $this;
    }

    /**
     * Checks if the storage is initialized.
     *
     * @return bool
     */
    public function initialized() : bool
    {
        return $this->initialized;
    }

    /**
     * Returns the DataAdapter instance.
     *
     * @return ConnectorInterface
     */
    public function getConnector() : ConnectorInterface
    {
        return $this->connector;
    }

    /**
     * Creates an empty entity. Should be use by getters.
     *
     * @return DataEntityInterface
     */
    public function createEntity() : DataEntityInterface
    {
        return clone $this->dataEntityPrototype;
    }

    /**
     * Saves data.
     *
     * @param DataEntityInterface &$dataEntity
     * @return StorageInterface
     */
    public function saveEntity(DataEntityInterface&$dataEntity) : StorageInterface
    {
        $entityClass = get_class($dataEntity);
        $storageEntityClass = get_class($this->dataEntityPrototype);

        if ($entityClass != $storageEntityClass) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot use %s with this data storage class. You must use %s.',
                    $entityClass,
                    $storageEntityClass
                ),
                1000
            );
        }

        $dataId = $this->getConnector()->saveData($dataEntity->getKeyData(), $this->getEntityData($dataEntity));

        // If key data is empty, then it was an insert. Get a new entity with all data.
        if ($dataId && empty($dataEntity->getKeyData())) {
            $entityData = $this->getConnector()->getData($dataId);
            $this->populateEntity($dataEntity, $entityData);
        }

        return $this;
    }

    /**
     * Gets one Entity from the data adapter by expression.
     *
     * @param array $expression
     * @return null|DataEntityInterface
     */
    protected function getDataEntity(array $expression) : ? DataEntityInterface
    {
        $entity = null;
        $entityList = $this->getDataEntitySet($expression, [ConnectorInterface::OPTION_LIMIT => 1]);

        if (!empty($entityList)) {
            $entity = $entityList[0];
        }

        return $entity;
    }

    /**
     * Gets a set of Entities from the data adapter by expression.
     *
     * @param array $expression
     * @param array $options
     * @return DataEntityInterface[]
     */
    protected function getDataEntitySet(array $expression, array $options = []) : array
    {
        $dataList = $this->getConnector()->getDataSet($expression, $options);

        return $this->getEntitySetFromDataSet($dataList);
    }

    /**
     * Gets entity list from data storage set.
     *
     * @param array $dataList
     * @return DataEntityInterface[]
     */
    protected function getEntitySetFromDataSet(array $dataList) : array
    {
        $entityList = [];

        foreach ($dataList as $entityData) {
            /** @var DataEntityInterface $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $entityData);
            $entityList[] = $entity;
        }

        return $entityList;
    }

    /**
     * Get data from an entity.
     *
     * @param DataEntityInterface $dataEntity
     * @return array
     */
    abstract protected function getEntityData(DataEntityInterface $dataEntity) : array;

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $dataEntity
     * @param array               $data
     * @return void
     */
    abstract protected function populateEntity(DataEntityInterface&$dataEntity, array $data) : void;
}
