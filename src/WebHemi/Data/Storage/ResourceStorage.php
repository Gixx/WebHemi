<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
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
     * @return EntitySet
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

        return $this->getEntitySet(ResourceEntity::class, $data);
    }

    /**
     * Returns resource information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|ResourceEntity
     */
    public function getResourceById(int $identifier) : ? ResourceEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getResourceById',
            [
                ':idResource' => $identifier
            ]
        );

        /** @var null|ResourceEntity $entity */
        $entity = $this->getEntity(ResourceEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns resource information by name.
     *
     * @param  string $name
     * @return null|ResourceEntity
     */
    public function getResourceByName(string $name) : ? ResourceEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getResourceByName',
            [
                ':name' => $name
            ]
        );

        /** @var null|ResourceEntity $entity */
        $entity = $this->getEntity(ResourceEntity::class, $data[0] ?? []);

        return $entity;
    }
}
