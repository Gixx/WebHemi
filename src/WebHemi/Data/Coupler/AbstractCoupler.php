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
declare(strict_types = 1);

namespace WebHemi\Data\Coupler;

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Data\CouplerInterface;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\ConnectorInterface;

/**
 * Class AbstractCoupler.
 */
abstract class AbstractCoupler implements CouplerInterface
{
    /** @var ConnectorInterface */
    private $connector;
    /** @var EntityInterface[] */
    protected $dataEntityPrototypes = [];
    /** @var string */
    protected $connectorIdKey;
    /** @var string */
    protected $connectorDataGroup;
    /** @var array */
    protected $dependentDataGroups;

    /**
     * AbstractCoupler constructor.
     *
     * @param ConnectorInterface $connector
     * @param EntityInterface    $dataEntityPrototypeA
     * @param EntityInterface    $dataEntityPrototypeB
     */
    public function __construct(
        ConnectorInterface $connector,
        EntityInterface $dataEntityPrototypeA,
        EntityInterface $dataEntityPrototypeB
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

        $this->connector = $connector;
        $this->dataEntityPrototypes[$entityClassA] = $dataEntityPrototypeA;
        $this->dataEntityPrototypes[$entityClassB] = $dataEntityPrototypeB;
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
     * Gets all the entities those are depending from the given entity.
     *
     * @param EntityInterface $dataEntity
     * @throws RuntimeException
     * @return EntityInterface[]
     */
    public function getEntityDependencies(EntityInterface $dataEntity) : array
    {
        $entityClass = get_class($dataEntity);
        if (!isset($this->dataEntityPrototypes[$entityClass])) {
            throw new RuntimeException(
                sprintf('Cannot use this coupler class to find dependencies for %s.', $entityClass),
                1001
            );
        }

        $entityList = [];
        $dataList = $this->getEntityDataSet($dataEntity);

        foreach ($dataList as $entityData) {
            $entityList[] = $this->getDependingEntity($dataEntity, $entityData);
        }

        return $entityList;
    }

    /**
     * Sets dependency for the entities
     *
     * @param EntityInterface $dataEntityA
     * @param EntityInterface $dataEntityB
     * @return int The ID of the saved entity in the storage
     */
    public function setDependency(EntityInterface $dataEntityA, EntityInterface $dataEntityB) : int
    {
        $entityClassA = get_class($dataEntityA);
        if (!isset($this->dataEntityPrototypes[$entityClassA])) {
            throw new InvalidArgumentException(sprintf('Cannot use this coupler class for %s.', $entityClassA), 1002);
        }

        $entityClassB = get_class($dataEntityB);
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
            $this->dependentDataGroups[$entityClassA]['source_key'] => $dataEntityA->getKeyData(),
            $this->dependentDataGroups[$entityClassB]['source_key'] => $dataEntityB->getKeyData(),
        ];

        // Point the data adapter to the connector group
        return $this->getConnector()
            ->setDataGroup($this->connectorDataGroup)
            ->setIdKey($this->connectorIdKey)
            ->saveData(null, $data);
    }

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param EntityInterface $referenceEntity
     * @param array           $entityData
     * @return EntityInterface
     */
    abstract protected function getDependingEntity(
        EntityInterface $referenceEntity,
        array $entityData
    ) : EntityInterface;

    /**
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return EntityInterface
     */
    protected function getNewEntityInstance(string $entityClassName) : EntityInterface
    {
        return clone $this->dataEntityPrototypes[$entityClassName];
    }

    /**
     * Gets raw depending entity data list for the given entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityDataSet(EntityInterface $dataEntity) : array
    {
        $entityClassName = get_class($dataEntity);
        $entityDataSet = [];
        $identifiers = [];

        $this->getConnector()->setDataGroup($this->connectorDataGroup)
            ->setIdKey($this->connectorIdKey);

        $dataList = $this->getConnector()->getDataSet([
            $this->dependentDataGroups[$entityClassName]['source_key'].' = ?' => $dataEntity->getKeyData()
        ]);

        foreach ($dataList as $rowData) {
            $identifiers[] = $rowData[$this->dependentDataGroups[$entityClassName]['connector_key']];
        }

        if (!empty($identifiers)) {
            $this->getConnector()->setDataGroup($this->dependentDataGroups[$entityClassName]['depending_group'])
                ->setIdKey($this->dependentDataGroups[$entityClassName]['depending_id_key']);

            $entityDataSet = $this->getConnector()->getDataSet([
                $this->dependentDataGroups[$entityClassName]['depending_id_key'].' IN (?)' => $identifiers
            ]);
        }

        return $entityDataSet;
    }
}
