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
 * Class ResourceEntity
 */
class ResourceEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_resource' => null,
        'name' => null,
        'title' => null,
        'description' => null,
        'type' => null,
        'is_read_only' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return ResourceEntity
     */
    public function setResourceId(int $identifier) : ResourceEntity
    {
        $this->container['id_resource'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getResourceId() : ? int
    {
        return !is_null($this->container['id_resource'])
            ? (int) $this->container['id_resource']
            : null;
    }

    /**
     * @param string $name
     * @return ResourceEntity
     */
    public function setName(string $name) : ResourceEntity
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName() : ? string
    {
        return $this->container['name'];
    }

    /**
     * @param string $title
     * @return ResourceEntity
     */
    public function setTitle(string $title) : ResourceEntity
    {
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->container['title'];
    }

    /**
     * @param string $description
     * @return ResourceEntity
     */
    public function setDescription(string $description) : ResourceEntity
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
     * @param string $type
     * @return ResourceEntity
     */
    public function setType(string $type) : ResourceEntity
    {
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        return $this->container['type'];
    }

    /**
     * @param bool $isReadonly
     * @return ResourceEntity
     */
    public function setIsReadOnly(bool $isReadonly) : ResourceEntity
    {
        $this->container['is_read_only'] = $isReadonly ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsReadOnly() : bool
    {
        return !empty($this->container['is_read_only']);
    }

    /**
     * @param DateTime $dateTime
     * @return ResourceEntity
     */
    public function setDateCreated(DateTime $dateTime) : ResourceEntity
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
     * @return ResourceEntity
     */
    public function setDateModified(DateTime $dateTime) : ResourceEntity
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
