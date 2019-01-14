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

use WebHemi\Data\Entity\DomainEntity;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class DomainStorage.
 */
class DomainStorage extends AbstractStorage
{
    /**
     * Returns every Domain entity.
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return EntitySet
     */
    public function getDomainList(
        ? int $limit = null,
        ? int $offset = null
    ) : EntitySet {
        $limit = $limit ?? QueryInterface::MAX_ROW_LIMIT;
        $offset = $offset ?? 0;

        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getDomainList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(DomainEntity::class, $data);
    }

    /**
     * Returns a Domain entity identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|DomainEntity
     */
    public function getDomainById(int $identifier) : ? DomainEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getDomainById',
            [
                ':idDomain' => $identifier
            ]
        );

        /** @var null|DomainEntity $entity */
        $entity = $this->getEntity(DomainEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns an Domain entity by name.
     *
     * @param  string $name
     * @return null|DomainEntity
     */
    public function getDomainByDomainName(string $name) : ? DomainEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getDomainByDomainName',
            [
                ':name' => $name
            ]
        );

        /** @var null|DomainEntity $entity */
        $entity = $this->getEntity(DomainEntity::class, $data[0] ?? []);

        return $entity;
    }
}
