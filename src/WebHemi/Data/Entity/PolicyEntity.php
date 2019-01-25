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
 * Class PolicyEntity
 */
class PolicyEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_policy' => null,
        'fk_resource' => null,
        'fk_application' => null,
        'name' => null,
        'title' => null,
        'description' => null,
        'method' => null,
        'is_read_only' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return PolicyEntity
     */
    public function setPolicyId(int $identifier) : PolicyEntity
    {
        $this->container['id_policy'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPolicyId() : ? int
    {
        return !is_null($this->container['id_policy'])
            ? (int) $this->container['id_policy']
            : null;
    }

    /**
     * @param int $resourceIdentifier
     * @return PolicyEntity
     */
    public function setResourceId(int $resourceIdentifier) : PolicyEntity
    {
        $this->container['fk_resource'] = $resourceIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getResourceId() : ? int
    {
        return !is_null($this->container['fk_resource'])
            ? (int) $this->container['fk_resource']
            : null;
    }

    /**
     * @param int $applicationIdentifier
     * @return PolicyEntity
     */
    public function setApplicationId(int $applicationIdentifier) : PolicyEntity
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
     * @return PolicyEntity
     */
    public function setName(string $name) : PolicyEntity
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
     * @return PolicyEntity
     */
    public function setTitle(string $title) : PolicyEntity
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
     * @return PolicyEntity
     */
    public function setDescription(string $description) : PolicyEntity
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
     * @param bool $isReadonly
     * @return PolicyEntity
     */
    public function setIsReadOnly(bool $isReadonly) : PolicyEntity
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
     * @return PolicyEntity
     */
    public function setDateCreated(DateTime $dateTime) : PolicyEntity
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
     * @return PolicyEntity
     */
    public function setDateModified(DateTime $dateTime) : PolicyEntity
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
