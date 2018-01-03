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
 * Class FilesystemCategoryEntity.
 */
class FilesystemCategoryEntity implements EntityInterface
{
    /** @var int */
    private $filesystemCategoryId;
    /** @var int */
    private $applicationId;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var string */
    private $itemOrder;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return FilesystemCategoryEntity
     */
    public function setKeyData(int $entityId) : FilesystemCategoryEntity
    {
        $this->filesystemCategoryId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->filesystemCategoryId;
    }

    /**
     * @param int $filesystemCategoryId
     * @return FilesystemCategoryEntity
     */
    public function setFilesystemCategoryId(int $filesystemCategoryId) : FilesystemCategoryEntity
    {
        $this->filesystemCategoryId = $filesystemCategoryId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFilesystemCategoryId() : ? int
    {
        return $this->filesystemCategoryId;
    }

    /**
     * @param null|int $applicationId
     * @return FilesystemCategoryEntity
     */
    public function setApplicationId(? int $applicationId) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setName(? string $name) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setTitle(? string $title) : FilesystemCategoryEntity
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
     * @param string $description
     * @return FilesystemCategoryEntity
     */
    public function setDescription(string $description) : FilesystemCategoryEntity
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
     * @param string $itemOrder
     * @return FilesystemCategoryEntity
     */
    public function setItemOrder(string $itemOrder) : FilesystemCategoryEntity
    {
        $this->itemOrder = $itemOrder;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getItemOrder() : ? string
    {
        return $this->itemOrder;
    }

    /**
     * @param DateTime $dateCreated
     * @return FilesystemCategoryEntity
     */
    public function setDateCreated(DateTime $dateCreated) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setDateModified(? DateTime $dateModified) : FilesystemCategoryEntity
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
