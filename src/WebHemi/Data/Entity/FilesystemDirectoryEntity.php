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
 * Class FilesystemDirectoryEntity
 */
class FilesystemDirectoryEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem_directory' => null,
        'description' => null,
        'directory_type' => null,
        'proxy' => null,
        'is_autoindex' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemDirectoryEntity
     */
    public function setFilesystemDirectoryId(int $identifier) : FilesystemDirectoryEntity
    {
        $this->container['id_filesystem_directory'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemDirectoryId() : ? int
    {
        return !is_null($this->container['id_filesystem_directory'])
            ? (int) $this->container['id_filesystem_directory']
            : null;
    }

    /**
     * @param string $description
     * @return FilesystemDirectoryEntity
     */
    public function setDescription(string $description) : FilesystemDirectoryEntity
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->container['description'];
    }

    /**
     * @param string $directoryType
     * @return FilesystemDirectoryEntity
     */
    public function setDirectoryType(string $directoryType) : FilesystemDirectoryEntity
    {
        $this->container['directory_type'] = $directoryType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDirectoryType() : ? string
    {
        return $this->container['directory_type'];
    }

    /**
     * @param string $proxy
     * @return FilesystemDirectoryEntity
     */
    public function setProxy(string $proxy) : FilesystemDirectoryEntity
    {
        $this->container['proxy'] = $proxy;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProxy() : ? string
    {
        return $this->container['proxy'];
    }

    /**
     * @param bool $isAutoindex
     * @return FilesystemDirectoryEntity
     */
    public function setIsAutoIndex(bool $isAutoindex) : FilesystemDirectoryEntity
    {
        $this->container['is_autoindex'] = $isAutoindex ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAutoIndex() : bool
    {
        return !empty($this->container['is_autoindex']);
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemDirectoryEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemDirectoryEntity
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
     * @return FilesystemDirectoryEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemDirectoryEntity
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
