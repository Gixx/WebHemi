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

namespace WebHemi\Auth\Storage;

use WebHemi\Auth\StorageInterface;
use WebHemi\Session\ServiceInterface as SessionInterface;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class Session
 */
class Session implements StorageInterface
{
    /** @var string */
    private $sessionKey = '_auth_identity';
    /** @var SessionInterface */
    private $sessionManager;

    /**
     * Session constructor.
     *
     * @param SessionInterface $sessionManager
     */
    public function __construct(SessionInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Sets the authenticated user.
     *
     * @param UserEntity $dataEntity
     * @return StorageInterface
     */
    public function setIdentity(UserEntity $dataEntity) : StorageInterface
    {
        // for safety purposes
        $this->sessionManager->regenerateId();
        // set user entity into the session as read-only
        $this->sessionManager->set($this->sessionKey, $dataEntity);

        return $this;
    }

    /**
     * Checks if there is any authenticated user.
     *
     * @return bool
     */
    public function hasIdentity() : bool
    {
        return $this->sessionManager->has($this->sessionKey);
    }

    /**
     * Gets the authenticated user.
     *
     * @return null|UserEntity
     */
    public function getIdentity() : ?UserEntity
    {
        return $this->sessionManager->get($this->sessionKey);
    }

    /**
     * Clears the session.
     *
     * @return StorageInterface
     */
    public function clearIdentity() : StorageInterface
    {
        // force delete read-only data.
        $this->sessionManager->delete($this->sessionKey, true);
        // for safety purposes
        $this->sessionManager->regenerateId();

        return $this;
    }
}
