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
namespace WebHemi\Data\Storage\Traits;

use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class GetEntityListFromDataSetTrait
 */
trait GetEntityListFromDataSetTrait
{
    /**
     * Creates an empty entity. Should be use by getters.
     *
     * @return DataEntityInterface
     */
    abstract public function createEntity();

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    abstract protected function populateEntity(DataEntityInterface&$entity, array $data);

    /**
     * Gets entity list from data storage set.
     *
     * @param bool|array $dataList
     * @return null|array<DataEntityInterface>
     */
    protected function getEntityListFromDataSet($dataList)
    {
        $entityList = null;

        if (!empty($dataList) && is_array($dataList)) {
            foreach ($dataList as $entityData) {
                /** @var DataEntityInterface $entity */
                $entity = $this->createEntity();
                $this->populateEntity($entity, $entityData);
                $entityList[] = $entity;
            }
        }

        return $entityList;
    }
}
