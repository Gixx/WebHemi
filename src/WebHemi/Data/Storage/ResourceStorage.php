<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Storage;

use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\ResourceEntity;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class ResourceStorage.
 */
class ResourceStorage extends AbstractStorage
{
    /**
     * Returns a full set of resources data.
     *
     * @param int $limit
     * @param int $offset
     * @return null|EntitySet
     */
    public function getResourceList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getResourceList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        $entitySet = $this->createEntitySet();

        foreach ($data as $row) {
            /** @var ResourceEntity $entity */
            $entity = $this->createEntity(ResourceEntity::class, $row);

            if (!empty($entity)) {
                $entitySet[] = $entity;
            }
        }

        return $entitySet;
    }

    /**
     * Returns resource information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|ResourceEntity
     */
    public function getResourceById(int $identifier) : ? ResourceEntity
    {
        $data = $this->getQueryAdapter()->fetchData('getResourceById', [':idResource' => $identifier]);

        if (isset($data[0])) {
            /** @var null|ResourceEntity $entity */
            $entity = $this->createEntity(ResourceEntity::class, $data[0] ?? []);
        }

        return $entity ?? null;
    }

    /**
     * Returns resource information by name.
     *
     * @param  string $name
     * @return null|ResourceEntity
     */
    public function getResourceByName(string $name) : ? ResourceEntity
    {
        $data = $this->getQueryAdapter()->fetchData('getResourceByName', [':name' => $name]);

        if (isset($data[0])) {
            /** @var null|ResourceEntity $entity */
            $entity = $this->createEntity(ResourceEntity::class, $data[0] ?? []);
        }

        return $entity ?? null;
    }
}
