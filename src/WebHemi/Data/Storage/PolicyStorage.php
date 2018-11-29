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
use WebHemi\Data\Entity\PolicyEntity;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class PolicyStorage.
 */
class PolicyStorage extends AbstractStorage
{
    /**
     * Returns a full set of policy data.
     *
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns a set of policy data identified by resource ID.
     *
     * @param  int $resourceId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyListByResource(
        int $resourceId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyListByResource',
            [
                ':idResource' => $resourceId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns a set of policy data identified by application ID.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns a set of policy data identified by user ID.
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyListByUser(
        int $userId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyListByUser',
            [
                ':idUser' => $userId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns a set of policy data identified by user group ID.
     *
     * @param int $userGroupId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyListByUserGroup(
        int $userGroupId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyListByUserGroup',
            [
                ':idUserGroup' => $userGroupId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns a set of policy data identified by both resource and application IDs.
     *
     * @param int $resourceId
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getPolicyListByResourceAndApplication(
        int $resourceId,
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyListByResourceAndApplication',
            [
                ':idResource' => $resourceId,
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(PolicyEntity::class, $data);
    }

    /**
     * Returns policy data identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|PolicyEntity
     */
    public function getPolicyById(int $identifier) : ? PolicyEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyById',
            [
                ':idPolicy' => $identifier
            ]
        );

        /** @var null|PolicyEntity $entity */
        $entity = $this->getEntity(PolicyEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns policy data by name.
     *
     * @param  string $name
     * @return null|PolicyEntity
     */
    public function getPolicyByName(string $name) : ? PolicyEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getPolicyByName',
            [
                ':name' => $name
            ]
        );

        /** @var null|PolicyEntity $entity */
        $entity = $this->getEntity(PolicyEntity::class, $data[0] ?? []);

        return $entity;
    }
}
