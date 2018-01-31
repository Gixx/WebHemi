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
     * @return null|array
     */
    public function getPolicyList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $policies = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getPolicyList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $policies[$row['name']] = $row;
        }

        return $policies;
    }

    /**
     * Returns policy data identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getPolicyById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData('getPolicyById', [':idPolicy' => $identifier]);

        return $data[0] ?? null;
    }

    /**
     * Returns policy data by name.
     *
     * @param  string $name
     * @return null|array
     */
    public function getPolicyByName(string $name) : ? array
    {
        $data = $this->queryAdapter->fetchData('getPolicyByName', [':name' => $name]);

        return $data[0] ?? null;
    }

    /**
     * Returns a set of policy data identified by resource ID.
     *
     * @param  int $resourceId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPolicyListByResource(
        int $resourceId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $policies = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getPolicyListByResource',
            [
                ':idResource' => $resourceId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $policies[$row['name']] = $row;
        }

        return $policies;
    }

    /**
     * Returns a set of policy data identified by application ID.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPolicyListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $policies = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getPolicyListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $policies[$row['name']] = $row;
        }

        return $policies;
    }

    /**
     * Returns a set of policy data identified by both resource and application IDs.
     *
     * @param int $resourceId
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPolicyListByResourceAndApplication(
        int $resourceId,
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $policies = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getPolicyListByResourceAndApplication',
            [
                ':idResource' => $resourceId,
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $policies[$row['name']] = $row;
        }

        return $policies;
    }
}
