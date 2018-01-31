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
 * Class ResourceStorage.
 */
class ResourceStorage extends AbstractStorage
{
    /**
     * Returns a full set of resources data.
     *
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getResourceList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $resources = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getResourceList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $resources[$row['name']] = $row;
        }

        return $resources;
    }

    /**
     * Returns resource information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getResourceById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData('getResourceById', [':idResource' => $identifier]);

        return $data[0] ?? null;
    }

    /**
     * Returns resource information by name.
     *
     * @param  string $name
     * @return null|array
     */
    public function getResourceByName(string $name) : ? array
    {
        $data = $this->queryAdapter->fetchData('getResourceByName', [':name' => $name]);

        return $data[0] ?? null;
    }
}
