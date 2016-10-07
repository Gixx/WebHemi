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
     * DataCouplerInterface constructor.
     *
     * @param DataAdapterInterface $defaultAdapter
     * @param DataEntityInterface[] ...$dataEntityPrototypes
     */
    public function __construct(
        DataAdapterInterface $defaultAdapter,
        DataEntityInterface ...$dataEntityPrototypes
    ) {
        $this->defaultAdapter = $defaultAdapter;

        foreach ($dataEntityPrototypes as $entityPrototype) {
            $this->dataEntityPrototypes[get_class($entityPrototype)] = $entityPrototype;
        }
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
                sprintf('Cannot use this coupler class to find dependencies for %s.', $entityClass)
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
        if (!isset($this->dataEntityPrototypes[$entityClassName])) {
            throw new RuntimeException(sprintf('Class %s is not defined in this Coupler.', $entityClassName));
        }

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
