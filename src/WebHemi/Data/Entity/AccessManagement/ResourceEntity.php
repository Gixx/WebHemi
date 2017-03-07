<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity\AccessManagement;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class ResourceEntity.
 */
class ResourceEntity implements EntityInterface
{
    /** @var int */
    private $resourceId;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var bool */
    private $isReadOnly;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return ResourceEntity
     */
    public function setKeyData(int $entityId) : ResourceEntity
    {
        $this->resourceId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     * @return ResourceEntity
     */
    public function setResourceId(int $resourceId) : ResourceEntity
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getResourceId() : ? int
    {
        return $this->resourceId;
    }

    /**
     * @param string $name
     * @return ResourceEntity
     */
    public function setName(string $name) : ResourceEntity
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
     * @param string $title
     * @return ResourceEntity
     */
    public function setTitle(string $title) : ResourceEntity
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
     * @return ResourceEntity
     */
    public function setDescription(string $description) : ResourceEntity
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
     * @param bool $state
     * @return ResourceEntity
     */
    public function setReadOnly(bool $state) : ResourceEntity
    {
        $this->isReadOnly = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly() : bool
    {
        return $this->isReadOnly ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return ResourceEntity
     */
    public function setDateCreated(DateTime $dateCreated) : ResourceEntity
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
     * @param DateTime $dateModified
     * @return ResourceEntity
     */
    public function setDateModified(DateTime $dateModified) : ResourceEntity
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
