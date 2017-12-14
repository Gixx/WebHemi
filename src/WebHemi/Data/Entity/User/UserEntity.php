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
 * Class UserEntity.
 */
class UserEntity implements EntityInterface
{
    /** @var int */
    private $userId;
    /** @var string */
    private $userName;
    /** @var string */
    private $email;
    /** @var string */
    private $password;
    /** @var string */
    private $hash;
    /** @var bool */
    private $isActive;
    /** @var bool */
    private $isEnabled;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return UserEntity
     */
    public function setKeyData(int $entityId) : UserEntity
    {
        $this->userId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return UserEntity
     */
    public function setUserId(int $userId) : UserEntity
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
     * @param string $userName
     * @return UserEntity
     */
    public function setUserName(string $userName) : UserEntity
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserName() : ? string
    {
        return $this->userName;
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail(string $email) : UserEntity
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail() : ? string
    {
        return $this->email;
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword(string $password) : UserEntity
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword() : ? string
    {
        return $this->password;
    }

    /**
     * @param string $hash
     * @return UserEntity
     */
    public function setHash(string $hash) : UserEntity
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHash() : ? string
    {
        return $this->hash;
    }

    /**
     * @param bool $state
     * @return UserEntity
     */
    public function setActive(bool $state) : UserEntity
    {
        $this->isActive = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive() : bool
    {
        return $this->isActive ?? false;
    }

    /**
     * @param bool $state
     * @return UserEntity
     */
    public function setEnabled(bool $state) : UserEntity
    {
        $this->isEnabled = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled() : bool
    {
        return $this->isEnabled ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return UserEntity
     */
    public function setDateCreated(DateTime $dateCreated) : UserEntity
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
     * @return UserEntity
     */
    public function setDateModified(? DateTime $dateModified) : UserEntity
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
