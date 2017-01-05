<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Data\Entity\User;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class UserMetaEntity.
 */
class UserMetaEntity implements DataEntityInterface
{
    /** @var int */
    private $userMetaId;
    /** @var int */
    private $userId;
    /** @var string */
    private $metaKey;
    /** @var string */
    private $metaData;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return UserMetaEntity
     */
    public function setKeyData($entityId)
    {
        $this->userMetaId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData()
    {
        return $this->userMetaId;
    }

    /**
     * @param int $userMetaId
     *
     * @return UserMetaEntity
     */
    public function setUserMetaId($userMetaId)
    {
        $this->userMetaId = $userMetaId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserMetaId()
    {
        return $this->userMetaId;
    }

    /**
     * @param int $userId
     *
     * @return UserMetaEntity
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $metaKey
     *
     * @return UserMetaEntity
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param mixed $metaData
     *
     * @return UserMetaEntity
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return UserMetaEntity
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
     * @return UserMetaEntity
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
