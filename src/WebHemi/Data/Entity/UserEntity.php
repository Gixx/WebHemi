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
 * Class UserEntity
 */
class UserEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_user' => null,
        'username' => null,
        'email' => null,
        'password' => null,
        'hash' => null,
        'is_active' => null,
        'is_enabled' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return UserEntity
     */
    public function setUserId(int $identifier) : UserEntity
    {
        $this->container['id_user'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId() : ? int
    {
        return !is_null($this->container['id_user'])
            ? (int) $this->container['id_user']
            : null;
    }

    /**
     * @param string $userName
     * @return UserEntity
     */
    public function setUserName(string $userName) : UserEntity
    {
        $this->container['username'] = $userName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserName() : ? string
    {
        return $this->container['username'];
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail(string $email) : UserEntity
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail() : ? string
    {
        return $this->container['email'];
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword(string $password) : UserEntity
    {
        $this->container['password'] = $password;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword() : ? string
    {
        return $this->container['password'];
    }

    /**
     * @param string $hash
     * @return UserEntity
     */
    public function setHash(string $hash) : UserEntity
    {
        $this->container['hash'] = $hash;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHash() : ? string
    {
        return $this->container['hash'];
    }

    /**
     * @param bool $isActive
     * @return UserEntity
     */
    public function setIsActive(bool $isActive) : UserEntity
    {
        $this->container['is_active'] = $isActive ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive() : bool
    {
        return !empty($this->container['is_active']);
    }

    /**
     * @param bool $isEnabled
     * @return UserEntity
     */
    public function setIsEnabled(bool $isEnabled) : UserEntity
    {
        $this->container['is_enabled'] = $isEnabled ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled() : bool
    {
        return !empty($this->container['is_enabled']);
    }

    /**
     * @param DateTime $dateTime
     * @return UserEntity
     */
    public function setDateCreated(DateTime $dateTime) : UserEntity
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
     * @return UserEntity
     */
    public function setDateModified(DateTime $dateTime) : UserEntity
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
