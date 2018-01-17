<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Auth;

/**
 * Interface ResultInterface
 */
interface ResultInterface
{
    public const FAILURE = 0;
    public const FAILURE_IDENTITY_NOT_FOUND = -1;
    public const FAILURE_IDENTITY_DISABLED = -2;
    public const FAILURE_IDENTITY_INACTIVE = -3;
    public const FAILURE_CREDENTIAL_INVALID = -4;
    public const FAILURE_OTHER = -1000;
    public const SUCCESS = 1;

    /**
     * Checks the authentication result.
     *
     * @return bool
     */
    public function isValid() : bool;

    /**
     * Sets the result code.
     *
     * @param  int $code
     * @return ResultInterface
     */
    public function setCode(int $code) : ResultInterface;

    /**
     * Gets the result code.
     *
     * @return int
     */
    public function getCode() : int;

    /**
     * Gets the result message.
     *
     * @return string
     */
    public function getMessage() : string;
}
