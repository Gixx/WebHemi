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

use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Interface StorageInterface.
 */
interface AuthStorageInterface
{
    /**
     * Sets the authenticated user.
     *
     * @param DataEntityInterface $dataEntity
     * @return AuthStorageInterface
     */
    public function setIdentity(DataEntityInterface $dataEntity) : AuthStorageInterface;

    /**
     * Checks if there is any authenticated user.
     *
     * @return bool
     */
    public function hasIdentity() : bool;

    /**
     * Gets the authenticated user.
     *
     * @return DataEntityInterface|null
     */
    public function getIdentity() : ?DataEntityInterface;

    /**
     * Clears the storage.
     *
     * @return AuthStorageInterface
     */
    public function clearIdentity() : AuthStorageInterface;
}
