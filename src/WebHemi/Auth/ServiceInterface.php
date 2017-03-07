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

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param ResultInterface        $authResultPrototype
     * @param StorageInterface       $authStorage
     * @param UserStorage            $dataStorage
     */
    public function __construct(
        ConfigurationInterface $configuration,
        ResultInterface $authResultPrototype,
        StorageInterface $authStorage,
        UserStorage $dataStorage
    );

    /**
     * Authenticates the user.
     *
     * @param CredentialInterface $credential
     * @return ResultInterface
     */
    public function authenticate(CredentialInterface $credential) : ResultInterface;

    /**
     * Sets the authenticated user.
     *
     * @param UserEntity $dataEntity
     * @return ServiceInterface
     */
    public function setIdentity(UserEntity $dataEntity) : ServiceInterface;

    /**
     * Checks whether the user is authenticated or not.
     *
     * @return bool
     */
    public function hasIdentity() : bool;

    /**
     * Gets the authenticated user's entity.
     *
     * @return UserEntity|null
     */
    public function getIdentity() : ? UserEntity;

    /**
     * Clears the session.
     *
     * @return ServiceInterface
     */
    public function clearIdentity() : ServiceInterface;
}
