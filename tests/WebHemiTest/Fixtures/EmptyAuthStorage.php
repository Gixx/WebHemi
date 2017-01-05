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

namespace WebHemiTest\Fixtures;

use WebHemi\Auth\AuthStorageInterface;
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
     * @return EmptyAuthStorage
     */
    public function setIdentity(DataEntityInterface $dataEntity)
    {
        $this->identity = $dataEntity;

        return $this;
    }

    /**
     * Gets the authenticated user.
     *
     * @return DataEntityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Clears the session.
     *
     * @return EmptyAuthStorage
     */
    public function clearIdentity()
    {
        $this->identity = null;

        return $this;
    }
}
