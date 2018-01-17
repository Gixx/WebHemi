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

namespace WebHemi\Auth\ServiceAdapter\Base;

use WebHemi\Auth\CredentialInterface;
use WebHemi\Auth\ResultInterface;
use WebHemi\Auth\ServiceAdapter\AbstractServiceAdapter;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractServiceAdapter
{
    /**
     * Authenticates the user.
     *
     * @param  CredentialInterface $credential
     * @return ResultInterface
     */
    public function authenticate(CredentialInterface $credential) : ResultInterface
    {
        /**
         * @var ResultInterface $result
         */
        $result = $this->getNewAuthResultInstance();
        $credentials = $credential->getCredentials();

        /**
         * @var UserStorage $dataStorage
         */
        $dataStorage = $this->getDataStorage();
        $user = $dataStorage->getUserByUserName($credentials['username']);

        if (!$user instanceof UserEntity) {
            $result->setCode(ResultInterface::FAILURE_IDENTITY_NOT_FOUND);
        } elseif (!$user->getEnabled()) {
            $result->setCode(ResultInterface::FAILURE_IDENTITY_DISABLED);
        } elseif (!$user->getActive()) {
            $result->setCode(ResultInterface::FAILURE_IDENTITY_INACTIVE);
        } elseif (!password_verify($credentials['password'], $user->getPassword())) {
            $result->setCode(ResultInterface::FAILURE_CREDENTIAL_INVALID);
        } else {
            $this->setIdentity($user);
            $result->setCode(ResultInterface::SUCCESS);
        }

        return $result;
    }
}
