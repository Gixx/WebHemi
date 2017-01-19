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

namespace WebHemi\Auth;

use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class Result.
 */
final class Result
{
    public const FAILURE = 0;
    public const FAILURE_IDENTITY_NOT_FOUND = -1;
    public const FAILURE_CREDENTIAL_INVALID = -2;
    public const FAILURE_OTHER = -3;
    public const SUCCESS = 1;

    /** @var int */
    private $code;
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
    public function isValid() : bool
    {
        return $this->code == 1;
    }

    /**
     * Sets the result code.
     *
     * @param int $code
     * @return Result
     */
    public function setCode(int $code) : Result
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
    public function getCode() : int
    {
        return $this->code;
    }

    /**
     * Gets the result message.
     *
     * @return string
     */
    public function getMessage() : string
    {
        return $this->messages[$this->code];
    }
}
