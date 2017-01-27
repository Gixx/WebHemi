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

namespace WebHemi\Auth\Credential;

use InvalidArgumentException;
use WebHemi\Adapter\Auth\AuthCredentialInterface;

/**
 * Interface NameAndPasswordCredential
 */
class NameAndPasswordCredential implements AuthCredentialInterface
{
    /** @var string */
    private $username = '';
    /** @var string */
    private $password = '';

    /**
     * Set a credential.
     *
     * @param string $key
     * @param string $value
     * @throws InvalidArgumentException
     * @return AuthCredentialInterface
     */
    public function addCredential(string $key, string $value) : AuthCredentialInterface
    {
        switch ($key) {
            case 'username':
                $this->username = $value;
                break;

            case 'password':
                $this->password = $value;
                break;

            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Parameter #1 must be either "usenrame" or "password", %s given.',
                        $key
                    ),
                    1000
                );
        }

        return $this;
    }

    /**
     * Returns the credentials in a key => value array.
     *
     * @return array
     */
    public function getCredentials() : array
    {
        return ['username' => $this->username, 'password' => $this->password];
    }
}
