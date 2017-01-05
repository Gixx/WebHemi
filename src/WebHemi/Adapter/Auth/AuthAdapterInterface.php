<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\Adapter\Auth;

use WebHemi\Auth\Result;
use WebHemi\Auth\AuthStorageInterface;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Storage\DataStorageInterface;

/**
 * Interface AuthAdapterInterface.
 */
interface AuthAdapterInterface
{
    /**
     * AuthAdapterInterface constructor.
     *
     * @param ConfigInterface      $configuration
     * @param Result               $authResultPrototype
     * @param AuthStorageInterface $authStorage
     * @param DataStorageInterface $dataStorage
     */
    public function __construct(
        ConfigInterface $configuration,
        Result $authResultPrototype,
        AuthStorageInterface $authStorage,
        DataStorageInterface $dataStorage
    );

    /**
     * Authenticates the user.
     *
     * @return Result
     */
    public function authenticate();

    /**
     * Checks whether the user is authenticated or not.
     *
     * @return bool
     */
    public function hasIdentity();

    /**
     * Sets the authenticated user.
     *
     * @param DataEntityInterface $dataEntity
     * @return AuthAdapterInterface
     */
    public function setIdentity(DataEntityInterface $dataEntity);

    /**
     * Gets the authenticated user's entity.
     *
     * @return null|string|DataEntityInterface
     */
    public function getIdentity();

    /**
     * Clears the session.
     *
     * @return AuthAdapterInterface
     */
    public function clearIdentity();
}
