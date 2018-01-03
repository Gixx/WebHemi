<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Auth;

use WebHemi\Data\Entity\User\UserEntity;

/**
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * Sets the authenticated user.
     *
     * @param UserEntity $dataEntity
     * @return StorageInterface
     */
    public function setIdentity(UserEntity $dataEntity) : StorageInterface;

    /**
     * Checks if there is any authenticated user.
     *
     * @return bool
     */
    public function hasIdentity() : bool;

    /**
     * Gets the authenticated user.
     *
     * @return UserEntity|null
     */
    public function getIdentity() : ? UserEntity;

    /**
     * Clears the storage.
     *
     * @return StorageInterface
     */
    public function clearIdentity() : StorageInterface;
}
