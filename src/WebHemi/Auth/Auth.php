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

use WebHemi\Adapter\Auth\AbstractAuthAdapter;
use WebHemi\Adapter\Auth\AuthCredentialInterface;
use WebHemi\Adapter\Auth\AuthResultInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;

/**
 * Class Auth
 */
final class Auth extends AbstractAuthAdapter
{
    /**
     * Authenticates the user.
     *
     * @param AuthCredentialInterface $credential
     * @return AuthResultInterface
     */
    public function authenticate(AuthCredentialInterface $credential) : AuthResultInterface
    {
        /** @var AuthResultInterface $result */
        $result = $this->getNewAuthResultInstance();
        $credentials = $credential->getCredentials();

        /** @var UserStorage $dataStorage */
        $dataStorage = $this->getDataStorage();
        $user = $dataStorage->getUserByUserName($credentials['username']);

        if (!$user instanceof UserEntity) {
            $result->setCode(AuthResultInterface::FAILURE_IDENTITY_NOT_FOUND);
        } elseif (!password_verify($credentials['password'], $user->getPassword())) {
            $result->setCode(AuthResultInterface::FAILURE_CREDENTIAL_INVALID);
        } else {
            $this->setIdentity($user);
            $result->setCode(AuthResultInterface::SUCCESS);
        }

        return $result;
    }
}
