<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Data\Entity\User;

use DateTime;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class UserGroupEntity.
 */
class UserGroupEntity implements DataEntityInterface
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
    public function setKeyData($entityId)
    {
        $this->userGroupId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData()
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     *
     * @return UserGroupEntity
     */
    public function setUserGroupId($userGroupId)
    {
        $this->userGroupId = $userGroupId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserGroupId()
    {
        return $this->userGroupId;
    }

    /**
     * @param string $name
     *
     * @return UserGroupEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $title
     *
     * @return UserGroupEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @return UserGroupEntity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param bool $state
     *
     * @return UserGroupEntity
     */
    public function setReadOnly($state)
    {
        $this->isReadOnly = (bool) $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return UserGroupEntity
     */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateModified
     *
     * @return UserGroupEntity
     */
    public function setDateModified(DateTime $dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
}
