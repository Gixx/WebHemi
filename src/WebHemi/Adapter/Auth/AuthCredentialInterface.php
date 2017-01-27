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

namespace WebHemi\Adapter\Auth;

/**
 * Interface AuthCredentialInterface
 */
interface AuthCredentialInterface
{
    /**
     * Set a credential.
     *
     * @param string $key
     * @param string $value
     * @return AuthCredentialInterface
     */
    public function addCredential(string $key, string $value) : AuthCredentialInterface;

    /**
     * Returns the credentials in a key => value array.
     *
     * @return array
     */
    public function getCredentials() : array;
}
