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
declare(strict_types=1);

namespace WebHemi\Auth\Storage;

use WebHemi\Application\SessionManager;
use WebHemi\Auth\AuthStorageInterface;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class Session
 */
class Session implements AuthStorageInterface
{
    /** @var string */
    private $sessionKey = '_auth_identity';
    /** @var SessionManager */
    private $sessionManager;

    /**
     * Session constructor.
     *
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Sets the authenticated user.
     *
     * @param DataEntityInterface $dataEntity
     * @return AuthStorageInterface
     */
    public function setIdentity(DataEntityInterface $dataEntity) : AuthStorageInterface
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
     * @return null|DataEntityInterface
     */
    public function getIdentity() : ?DataEntityInterface
    {
        return $this->sessionManager->get($this->sessionKey);
    }

    /**
     * Clears the session.
     *
     * @return AuthStorageInterface
     */
    public function clearIdentity() : AuthStorageInterface
    {
        // force delete read-only data.
        $this->sessionManager->delete($this->sessionKey, true);
        // for safety purposes
        $this->sessionManager->regenerateId();

        return $this;
    }
}
