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
    public function setIdentity(DataEntityInterface $dataEntity);

    /**
     * Gets the authenticated user.
     *
     * @return DataEntityInterface
     */
    public function getIdentity();

    /**
     * Clears the session.
     *
     * @return AuthStorageInterface
     */
    public function clearIdentity();
}
