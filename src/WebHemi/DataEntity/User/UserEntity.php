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
namespace WebHemi\DataEntity\User;

use DateTime;
use WebHemi\DataEntity\DataEntityInterface;

/**
 * Class UserEntity.
 */
class UserEntity implements DataEntityInterface
{
    /** @var string */
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
     * @param mixed $userId
     *
     * @return UserEntity
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userName
     *
     * @return UserEntity
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $email
     *
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $password
     *
     * @return UserEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $hash
     *
     * @return UserEntity
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param bool $state
     *
     * @return UserEntity
     */
    public function setActive($state)
    {
        $this->isActive = (bool) $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $state
     *
     * @return UserEntity
     */
    public function setEnabled($state)
    {
        $this->isEnabled = (bool) $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return UserEntity
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
     * @return UserEntity
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
