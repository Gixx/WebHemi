<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Auth\CredentialInterface;

/**
 * Class EmptyCoupler.
 */
class EmptyCredential implements CredentialInterface
{
    public $storage = [];

    /**
     * Set a credential.
     *
     * @param string $key
     * @param string $value
     * @return CredentialInterface
     */
    public function setCredential(string $key, string $value) : CredentialInterface
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
