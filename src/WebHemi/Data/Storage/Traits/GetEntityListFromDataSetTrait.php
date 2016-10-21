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
namespace WebHemi\Data\Storage\Traits;

use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class GetEntityListFromDataSetTrait
 */
trait GetEntityListFromDataSetTrait
{
    /**
     * Gets entity list from data storage set.
     *
     * @param bool|array $dataList
     * @return bool|array<DataEntityInterface>
     */
    protected function getEntityListFromDataSet($dataList)
    {
        $entityList = false;

        if (!empty($dataList)) {
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
