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
 * Class UserGroupEntity
 */
class UserGroupEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_user_group' => null,
        'name' => null,
        'title' => null,
        'introduction' => null,
        'description' => null,
        'is_read_only' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return UserGroupEntity
     */
    public function setUserGroupId(int $identifier) : UserGroupEntity
    {
        $this->container['id_user_group'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserGroupId() : ? int
    {
        return !is_null($this->container['id_user_group'])
            ? (int) $this->container['id_user_group']
            : null;
    }

    /**
     * @param string $name
     * @return UserGroupEntity
     */
    public function setName(string $name) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setTitle(string $title) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDescription(string $description) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setIsReadOnly(bool $isReadonly) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDateCreated(DateTime $dateTime) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDateModified(DateTime $dateTime) : UserGroupEntity
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
