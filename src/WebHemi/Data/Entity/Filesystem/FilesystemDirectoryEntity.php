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

namespace WebHemi\Data\Entity\Filesystem;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class FilesystemDirectoryEntity.
 */
class FilesystemDirectoryEntity implements EntityInterface
{
    /**
     * @var int
     */
    private $filesystemDirectoryId;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $directoryType;
    /**
     * @var string
     */
    private $proxy;
    /**
     * @var bool
     */
    private $isAutoIndex;
    /**
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @var DateTime
     */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param  int $entityId
     * @return FilesystemDirectoryEntity
     */
    public function setKeyData(int $entityId) : FilesystemDirectoryEntity
    {
        $this->filesystemDirectoryId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->filesystemDirectoryId;
    }

    /**
     * @param int $filesystemDirectoryId
     * @return FilesystemDirectoryEntity
     */
    public function setFilesystemDirectoryId(int $filesystemDirectoryId) : FilesystemDirectoryEntity
    {
        $this->filesystemDirectoryId = $filesystemDirectoryId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFilesystemDirectoryId() : ? int
    {
        return $this->filesystemDirectoryId;
    }

    /**
     * @param null|string $description
     * @return FilesystemDirectoryEntity
     */
    public function setDescription(? string $description) : FilesystemDirectoryEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->description;
    }

    /**
     * @param null|string $directoryType
     * @return FilesystemDirectoryEntity
     */
    public function setDirectoryType(? string $directoryType) : FilesystemDirectoryEntity
    {
        $this->directoryType = $directoryType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDirectoryType() : ? string
    {
        return $this->directoryType;
    }

    /**
     * @param null|string $proxy
     * @return FilesystemDirectoryEntity
     */
    public function setProxy(? string $proxy) : FilesystemDirectoryEntity
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProxy() : ? string
    {
        return $this->proxy;
    }

    /**
     * @param bool $state
     * @return FilesystemDirectoryEntity
     */
    public function setAutoIndex(bool $state) : FilesystemDirectoryEntity
    {
        $this->isAutoIndex = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoIndex() : bool
    {
        return $this->isAutoIndex ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return FilesystemDirectoryEntity
     */
    public function setDateCreated(DateTime $dateCreated) : FilesystemDirectoryEntity
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param null|DateTime $dateModified
     * @return FilesystemDirectoryEntity
     */
    public function setDateModified(? DateTime $dateModified) : FilesystemDirectoryEntity
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return $this->dateModified;
    }
}
