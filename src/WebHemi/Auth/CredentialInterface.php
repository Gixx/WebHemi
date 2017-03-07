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

/**
 * Interface CredentialInterface
 */
interface CredentialInterface
{
    /**
     * Set a credential.
     *
     * @param string $key
     * @param string $value
     * @return CredentialInterface
     */
    public function setCredential(string $key, string $value) : CredentialInterface;

    /**
     * Returns the credentials in a key => value array.
     *
     * @return array
     */
    public function getCredentials() : array;
}
