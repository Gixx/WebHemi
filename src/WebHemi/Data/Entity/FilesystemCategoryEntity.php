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
 * Class FilesystemCategoryEntity
 */
class FilesystemCategoryEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem_category' => null,
        'fk_application' => null,
        'name' => null,
        'title' => null,
        'description' => null,
        'item_order' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemCategoryEntity
     */
    public function setFilesystemCategoryId(int $identifier) : FilesystemCategoryEntity
    {
        $this->container['id_filesystem_category'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemCategoryId() : ? int
    {
        return !is_null($this->container['id_filesystem_category'])
            ? (int) $this->container['id_filesystem_category']
            : null;
    }

    /**
     * @param int $applicationIdentifier
     * @return FilesystemCategoryEntity
     */
    public function setApplicationId(int $applicationIdentifier) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setName(string $name) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setTitle(string $title) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setDescription(string $description) : FilesystemCategoryEntity
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
     * @param string $itemOrder
     * @return FilesystemCategoryEntity
     */
    public function setItemOrder(string $itemOrder) : FilesystemCategoryEntity
    {
        $this->container['item_order'] = $itemOrder;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getItemOrder() : ? string
    {
        return $this->container['item_order'];
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemCategoryEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemCategoryEntity
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
     * @return FilesystemCategoryEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemCategoryEntity
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
