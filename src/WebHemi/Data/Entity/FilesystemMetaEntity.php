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

namespace WebHemi\Data\Entity;

use WebHemi\DateTime;

/**
 * Class FilesystemMetaEntity
 */
class FilesystemMetaEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem_meta' => null,
        'fk_filesystem' => null,
        'meta_key' => null,
        'meta_data' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemMetaEntity
     */
    public function setFilesystemrMetaId(int $identifier) : FilesystemMetaEntity
    {
        $this->container['id_filesystem_meta'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemMetaId() : ? int
    {
        return !is_null($this->container['id_filesystem_meta'])
            ? (int) $this->container['id_filesystem_meta']
            : null;
    }

    /**
     * @param int $userIdentifier
     * @return FilesystemMetaEntity
     */
    public function setFilesystemId(int $userIdentifier) : FilesystemMetaEntity
    {
        $this->container['fk_filesystem'] = $userIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemId() : ? int
    {
        return !is_null($this->container['fk_filesystem'])
            ? (int) $this->container['fk_filesystem']
            : null;
    }

    /**
     * @param string $metaKey
     * @return FilesystemMetaEntity
     */
    public function setMetaKey(string $metaKey) : FilesystemMetaEntity
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
     * @return FilesystemMetaEntity
     */
    public function setMetaData(string $metaData) : FilesystemMetaEntity
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
     * @return FilesystemMetaEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemMetaEntity
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
     * @return FilesystemMetaEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemMetaEntity
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
