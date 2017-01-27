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
namespace WebHemiTest\Fixtures;

use WebHemi\Adapter\Auth\AuthCredentialInterface;

/**
 * Class EmptyCoupler.
 */
class EmptyCredential implements AuthCredentialInterface
{
    public $storage = [];

    /**
     * Set a credential.
     *
     * @param string $key
     * @param string $value
     * @return AuthCredentialInterface
     */
    public function addCredential(string $key, string $value) : AuthCredentialInterface
    {
        $this->storage[$key] = $value;

        return $this;
    }

    /**
     * Returns the credentials in a key => value array.
     *
     * @return array
     */
    public function getCredentials() : array
    {
        return $this->storage;
    }
}
