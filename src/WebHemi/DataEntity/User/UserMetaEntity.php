<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\DataEntity\User;

use WebHemi\DataEntity\DataEntityInterface;

/**
 * Class UserMetaEntity
 * @package WebHemi\DataEntity\User
 *
 * @property int $userMetaId
 * @property int $userId
 * @property string $metaKey
 * @property string $metaData
 */
class UserMetaEntity implements DataEntityInterface
{
    /** @var  string */
    private $userMetaId;
    /** @var  string */
    private $userId;
    /** @var  string */
    private $metaKey;
    /** @var  string */
    private $metaData;

    /**
     * @param mixed $userMetaId
     * @return $this
     */
    public function setUserMetaId($userMetaId)
    {
        $this->userMetaId = $userMetaId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserMetaId()
    {
        return $this->userMetaId;
    }

    /**
     * @param mixed $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $metaKey
     * @return $this
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param mixed $metaData
     * @return $this
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaData()
    {
        return $this->metaData;
    }
}
