<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity\Filesystem;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class FilesystemTagEntity.
 */
class FilesystemTagEntity implements EntityInterface
{
    /** @var int */
    private $filesystemTagId;
    /** @var int */
    private $applicationId;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return FilesystemTagEntity
     */
    public function setKeyData(int $entityId) : FilesystemTagEntity
    {
        $this->filesystemTagId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->filesystemTagId;
    }

    /**
     * @param int $filesystemTagId
     * @return FilesystemTagEntity
     */
    public function setFilesystemTagId(int $filesystemTagId) : FilesystemTagEntity
    {
        $this->filesystemTagId = $filesystemTagId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFilesystemTagId() : ? int
    {
        return $this->filesystemTagId;
    }

    /**
     * @param null|int $applicationId
     * @return FilesystemTagEntity
     */
    public function setApplicationId(? int $applicationId) : FilesystemTagEntity
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getApplicationId() : ? int
    {
        return $this->applicationId;
    }

    /**
     * @param null|string $name
     * @return FilesystemTagEntity
     */
    public function setName(? string $name) : FilesystemTagEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName() : ? string
    {
        return $this->name;
    }

    /**
     * @param null|string $title
     * @return FilesystemTagEntity
     */
    public function setTitle(? string $title) : FilesystemTagEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->title;
    }

    /**
     * @param null|string $description
     * @return FilesystemTagEntity
     */
    public function setDescription(? string $description) : FilesystemTagEntity
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
     * @param DateTime $dateCreated
     * @return FilesystemTagEntity
     */
    public function setDateCreated(DateTime $dateCreated) : FilesystemTagEntity
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
     * @return FilesystemTagEntity
     */
    public function setDateModified(? DateTime $dateModified) : FilesystemTagEntity
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
