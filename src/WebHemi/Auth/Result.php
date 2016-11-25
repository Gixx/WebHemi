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

namespace WebHemi\Auth;

use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class Result.
 */
class Result
{
    const FAILURE = 0;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_CREDENTIAL_INVALID = -2;
    const FAILURE_OTHER = -3;
    const SUCCESS = 1;

    /** @var int */
    private $code;
    /** @var null|UserEntity */
    private $userEntity;
    /** @var array */
    private $messages = [
        self::FAILURE => 'Authentication failed.',
        self::FAILURE_IDENTITY_NOT_FOUND => 'User is not found.',
        self::FAILURE_CREDENTIAL_INVALID => 'The provided credentials are not valid.',
        self::FAILURE_OTHER => 'Authentication failed because of unknown reason.',
        self::SUCCESS => 'Authenticated.',
    ];

    /**
     * Checks the authentication result.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->code == 1 && !empty($this->userEntity);
    }

    /**
     * Sets the result code.
     *
     * @param int $code
     * @return Result
     */
    public function setCode($code)
    {
        if (!isset($this->messages[$code])) {
            $code = -3;
        }

        $this->code = $code;

        return $this;
    }

    /**
     * Gets the result code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the authenticated user.
     *
     * @param UserEntity $userEntity
     * @return Result
     */
    public function setIdentity(UserEntity $userEntity)
    {
        $this->userEntity = $userEntity;

        return $this;
    }

    /**
     * Gets the authenticated user if any.
     *
     * @return UserEntity
     */
    public function getIdentity()
    {
        return $this->userEntity;
    }

    /**
     * Gets the result message.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->messages[$this->code];
    }
}
