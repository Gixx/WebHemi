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
 * Class FilesystemTagEntity
 */
class FilesystemTagEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem_tag' => null,
        'fk_application' => null,
        'name' => null,
        'title' => null,
        'description' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemTagEntity
     */
    public function setFilesystemTagId(int $identifier) : FilesystemTagEntity
    {
        $this->container['id_filesystem_tag'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemTagId() : ? int
    {
        return !is_null($this->container['id_filesystem_tag'])
            ? (int) $this->container['id_filesystem_tag']
            : null;
    }

    /**
     * @param int $applicationIdentifier
     * @return FilesystemTagEntity
     */
    public function setApplicationId(int $applicationIdentifier) : FilesystemTagEntity
    {
        $this->container['fk_application'] = $applicationIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationId() : ? int
    {
        return !is_null($this->container['fk_application'])
            ? (int) $this->container['fk_application']
            : null;
    }

    /**
     * @param string $name
     * @return FilesystemTagEntity
     */
    public function setName(string $name) : FilesystemTagEntity
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
     * @return FilesystemTagEntity
     */
    public function setTitle(string $title) : FilesystemTagEntity
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
     * @return FilesystemTagEntity
     */
    public function setDescription(string $description) : FilesystemTagEntity
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
     * @param DateTime $dateTime
     * @return FilesystemTagEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemTagEntity
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
     * @return FilesystemTagEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemTagEntity
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
