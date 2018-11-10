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

namespace WebHemi\CSRF;

use WebHemi\Session\ServiceInterface as SessionInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    public const SESSION_KEY = 'csrf';
    public const SESSION_TTL_IN_SECONDS = 10;

    /**
     * ServiceInterface constructor.
     *
     * @param SessionInterface $sessionManager
     */
    public function __construct(SessionInterface $sessionManager);

    /**
     * Generate a CSRF token.
     *
     * @return string
     */
    public function generate() : string;

    /**
     * Check the CSRF token is valid.
     *
     * @param string $token
     * @param null|int $ttl
     * @param bool $multiple
     * @return bool
     */
    public function verify(string $token, ? int $ttl = null, bool $multiple = true) : bool;
}
