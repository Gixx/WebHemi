<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

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
     * @return Session
     */
    public function setIdentity(DataEntityInterface $dataEntity)
    {
        // for safety purposes
        $this->sessionManager->regenerateId();
        // set user entity into the session as read-only
        $this->sessionManager->set($this->sessionKey, $dataEntity);

        return $this;
    }

    /**
     * Gets the authenticated user.
     *
     * @return null|DataEntityInterface
     */
    public function getIdentity()
    {
        return $this->sessionManager->get($this->sessionKey);
    }

    /**
     * Clears the session.
     *
     * @return Session
     */
    public function clearIdentity()
    {
        // force delete read-only data.
        $this->sessionManager->delete($this->sessionKey, true);
        // for safety purposes
        $this->sessionManager->regenerateId();

        return $this;
    }
}
