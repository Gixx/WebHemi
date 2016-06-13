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
    /** @var string */
    private $lastIp;
    /** @var string */
    private $registerIp;
    /** @var bool */
    private $isActive;
    /** @var bool */
    private $isEnabled;
    /** @var DateTime */
    private $timeLogin;
    /** @var DateTime */
    private $timeRegister;

    /**
     * @param mixed $userId
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @param string $lastIp
     *
     * @return $this
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * @param string $registerIp
     *
     * @return $this
     */
    public function setRegisterIp($registerIp)
    {
        $this->registerIp = $registerIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterIp()
    {
        return $this->registerIp;
    }

    /**
     * @param bool $state
     *
     * @return $this
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
     * @return $this
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
     * @param DateTime $timeLogin
     *
     * @return $this
     */
    public function setTimeLogin(DateTime $timeLogin)
    {
        $this->timeLogin = $timeLogin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimeLogin()
    {
        return $this->timeLogin;
    }

    /**
     * @param DateTime $timeRegister
     *
     * @return $this
     */
    public function setTimeRegister(DateTime $timeRegister)
    {
        $this->timeRegister = $timeRegister;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimeRegister()
    {
        return $this->timeRegister;
    }
}
