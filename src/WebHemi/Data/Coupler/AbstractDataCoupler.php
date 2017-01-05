<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Data\Coupler;

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class AbstractDataCoupler.
 */
abstract class AbstractDataCoupler implements DataCouplerInterface
{
    /** @var DataAdapterInterface */
    private $defaultAdapter;
    /** @var array<DataEntityInterface> */
    protected $dataEntityPrototypes = [];
    /** @var string */
    protected $connectorIdKey;
    /** @var string */
    protected $connectorDataGroup;
    /** @var array */
    protected $dependentDataGroups;

    /**
     * AbstractDataCoupler constructor.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface  $dataEntityPrototypeA
     * @param DataEntityInterface  $dataEntityPrototypeB
     */
    public function __construct(
        DataAdapterInterface $defaultAdapter,
        DataEntityInterface $dataEntityPrototypeA,
        DataEntityInterface $dataEntityPrototypeB
    ) {
        $entityClassA = get_class($dataEntityPrototypeA);
        $entityClassB = get_class($dataEntityPrototypeB);

        if (!isset($this->dependentDataGroups[$entityClassA])
            || !isset($this->dependentDataGroups[$entityClassB])
            || (count(array_keys($this->dependentDataGroups)) == 2 && $entityClassA == $entityClassB)
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'This coupler requires data entity instances from the following classes: %s; %s and %s are given.',
                    implode(', ', array_keys($this->dependentDataGroups)),
                    $entityClassA,
                    $entityClassB
                ),
                1000
            );
        }

        $this->defaultAdapter = $defaultAdapter;
        $this->dataEntityPrototypes[$entityClassA] = $dataEntityPrototypeA;
        $this->dataEntityPrototypes[$entityClassB] = $dataEntityPrototypeB;
    }

    /**
     * Returns the DataAdapter instance.
     *
     * @return DataAdapterInterface
     */
    public function getDataAdapter()
    {
        return $this->defaultAdapter;
    }

    /**
     * Gets all the entities those are depending from the given entity.
     *
     * @param DataEntityInterface $entity
     * @throws RuntimeException
     * @return array<DataEntityInterface>
     */
    public function getEntityDependencies(DataEntityInterface $entity)
    {
        $entityClass = get_class($entity);
        if (!isset($this->dataEntityPrototypes[$entityClass])) {
            throw new RuntimeException(
                sprintf('Cannot use this coupler class to find dependencies for %s.', $entityClass),
                1001
            );
        }

        $entityList = [];
        $dataList = $this->getEntityDataSet($entity);

        foreach ($dataList as $entityData) {
            $entityList[] = $this->getDependingEntity($entity, $entityData);
        }

        return $entityList;
    }

    /**
     * Sets dependency for the entities
     *
     * @param DataEntityInterface $entityA
     * @param DataEntityInterface $entityB
     * @return mixed The ID of the saved entity in the storage
     */
    public function setDependency(DataEntityInterface $entityA, DataEntityInterface $entityB)
    {
        $entityClassA = get_class($entityA);
        if (!isset($this->dataEntityPrototypes[$entityClassA])) {
            throw new InvalidArgumentException(sprintf('Cannot use this coupler class for %s.', $entityClassA), 1002);
        }

        $entityClassB = get_class($entityB);
        if (!isset($this->dataEntityPrototypes[$entityClassB])) {
            throw new InvalidArgumentException(sprintf('Cannot use this coupler class for %s.', $entityClassB), 1003);
        }

        if ($entityClassA == $entityClassB) {
            throw new InvalidArgumentException(
                sprintf('Cannot set dependency for the same type of entity %s.', $entityClassB),
                1004
            );
        }

        $data = [
            $this->dependentDataGroups[$entityClassA]['source_key'] => $entityA->getKeyData(),
            $this->dependentDataGroups[$entityClassB]['source_key'] => $entityB->getKeyData(),
        ];

        // Point the data adapter to the connector group
        return $this->getDataAdapter()
            ->setDataGroup($this->connectorDataGroup)
            ->setIdKey($this->connectorIdKey)
            ->saveData(null, $data);
    }

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param DataEntityInterface $referenceEntity
     * @param array               $entityData
     * @return DataEntityInterface
     */
    abstract protected function getDependingEntity(DataEntityInterface $referenceEntity, array $entityData);

    /**
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return DataEntityInterface
     */
    protected function getNewEntityInstance($entityClassName)
    {
        return clone $this->dataEntityPrototypes[$entityClassName];
    }

    /**
     * Gets raw depending entity data list for the given entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    protected function getEntityDataSet(DataEntityInterface $entity)
    {
        $entityClassName = get_class($entity);
        $entityDataSet = [];
        $identifiers = [];

        $this->getDataAdapter()->setDataGroup($this->connectorDataGroup)
            ->setIdKey($this->connectorIdKey);

        $dataList = $this->getDataAdapter()->getDataSet([
            $this->dependentDataGroups[$entityClassName]['source_key'].' = ?' => $entity->getKeyData()
        ]);

        foreach ($dataList as $rowData) {
            $identifiers[] = $rowData[$this->dependentDataGroups[$entityClassName]['connector_key']];
        }

        if (!empty($identifiers)) {
            $this->getDataAdapter()->setDataGroup($this->dependentDataGroups[$entityClassName]['depending_group'])
                ->setIdKey($this->dependentDataGroups[$entityClassName]['depending_id_key']);

            $entityDataSet = $this->getDataAdapter()->getDataSet([
                $this->dependentDataGroups[$entityClassName]['depending_id_key'].' IN (?)' => $identifiers
            ]);
        }

        return $entityDataSet;
    }
}
