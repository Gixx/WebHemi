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
 * Class UserMetaEntity.
 */
class UserMetaEntity implements EntityInterface
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
    public function setKeyData(int $entityId) : UserMetaEntity
    {
        $this->userMetaId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->userMetaId;
    }

    /**
     * @param int $userMetaId
     * @return UserMetaEntity
     */
    public function setUserMetaId(int $userMetaId) : UserMetaEntity
    {
        $this->userMetaId = $userMetaId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getUserMetaId() : ? int
    {
        return $this->userMetaId;
    }

    /**
     * @param int $userId
     * @return UserMetaEntity
     */
    public function setUserId(int $userId) : UserMetaEntity
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getUserId() : ? int
    {
        return $this->userId;
    }

    /**
     * @param string $metaKey
     * @return UserMetaEntity
     */
    public function setMetaKey(string $metaKey) : UserMetaEntity
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMetaKey() : ? string
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaData
     * @return UserMetaEntity
     */
    public function setMetaData(string $metaData) : UserMetaEntity
    {
        $this->metaData = $metaData;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMetaData() : ? string
    {
        return $this->metaData;
    }

    /**
     * @param DateTime $dateCreated
     * @return UserMetaEntity
     */
    public function setDateCreated(DateTime $dateCreated) : UserMetaEntity
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
     * @return UserMetaEntity
     */
    public function setDateModified(? DateTime $dateModified) : UserMetaEntity
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
