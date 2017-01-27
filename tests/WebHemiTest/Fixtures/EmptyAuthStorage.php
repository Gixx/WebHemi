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

namespace WebHemiTest\Fixtures;

use WebHemi\Adapter\Auth\AuthStorageInterface;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class EmptyAuthStorage
 */
class EmptyAuthStorage implements AuthStorageInterface
{
    private $identity;

    /**
     * Sets the authenticated user.
     *
     * @param DataEntityInterface $dataEntity
     * @return AuthStorageInterface
     */
    public function setIdentity(DataEntityInterface $dataEntity) : AuthStorageInterface
    {
        $this->identity = $dataEntity;

        return $this;
    }

    /**
     * Checks if there is any authenticated user.
     *
     * @return bool
     */
    public function hasIdentity() : bool
    {
        return !empty($this->identity);
    }

    /**
     * Gets the authenticated user.
     *
     * @return DataEntityInterface|null
     */
    public function getIdentity() : ?DataEntityInterface
    {
        return $this->identity;
    }

    /**
     * Clears the session.
     *
     * @return AuthStorageInterface
     */
    public function clearIdentity() : AuthStorageInterface
    {
        $this->identity = null;

        return $this;
    }
}
