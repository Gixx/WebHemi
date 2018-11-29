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

namespace WebHemi\Data\Entity;

use WebHemi\DateTime;

/**
 * Class UserMetaEntity
 */
class UserMetaEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_user_meta' => null,
        'fk_user' => null,
        'meta_key' => null,
        'meta_data' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return UserMetaEntity
     */
    public function setUserMetaId(int $identifier) : UserMetaEntity
    {
        $this->container['id_user_meta'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserMetaId() : ? int
    {
        return !is_null($this->container['id_user_meta'])
            ? (int) $this->container['id_user_meta']
            : null;
    }

    /**
     * @param int $userIdentifier
     * @return UserMetaEntity
     */
    public function setUserId(int $userIdentifier) : UserMetaEntity
    {
        $this->container['fk_user'] = $userIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId() : ? int
    {
        return !is_null($this->container['fk_user'])
            ? (int) $this->container['fk_user']
            : null;
    }

    /**
     * @param string $metaKey
     * @return UserMetaEntity
     */
    public function setMetaKey(string $metaKey) : UserMetaEntity
    {
        $this->container['meta_key'] = $metaKey;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMetaKey() : ? string
    {
        return $this->container['meta_key'];
    }

    /**
     * @param string $metaData
     * @return UserMetaEntity
     */
    public function setMetaData(string $metaData) : UserMetaEntity
    {
        $this->container['meta_data'] = $metaData;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMetaData() : ? string
    {
        return $this->container['meta_data'];
    }

    /**
     * @param DateTime $dateTime
     * @return UserMetaEntity
     */
    public function setDateCreated(DateTime $dateTime) : UserMetaEntity
    {
        $this->container['date_created'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return !empty($this->container['date_created'])
            ? new DateTime($this->container['date_created'])
            : null;
    }

    /**
     * @param DateTime $dateTime
     * @return UserMetaEntity
     */
    public function setDateModified(DateTime $dateTime) : UserMetaEntity
    {
        $this->container['date_modified'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return !empty($this->container['date_modified'])
            ? new DateTime($this->container['date_modified'])
            : null;
    }
}
