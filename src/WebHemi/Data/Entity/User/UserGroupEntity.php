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

namespace WebHemi\Data\Entity\User;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class UserGroupEntity.
 */
class UserGroupEntity implements EntityInterface
{
    /** @var int */
    private $userGroupId;
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
     * @return UserGroupEntity
     */
    public function setKeyData(int $entityId) : UserGroupEntity
    {
        $this->userGroupId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     * @return UserGroupEntity
     */
    public function setUserGroupId(int $userGroupId) : UserGroupEntity
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getUserGroupId() : ? int
    {
        return $this->userGroupId;
    }

    /**
     * @param string $name
     * @return UserGroupEntity
     */
    public function setName(string $name) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setTitle(string $title) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDescription(string $description) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setReadOnly(bool $state) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDateCreated(DateTime $dateCreated) : UserGroupEntity
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
     * @return UserGroupEntity
     */
    public function setDateModified(? DateTime $dateModified) : UserGroupEntity
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
