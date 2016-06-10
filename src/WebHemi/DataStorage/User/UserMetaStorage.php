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

namespace WebHemi\DataStorage\User;

use WebHemi\DataStorage\AbstractDataStorage;
use WebHemi\DataEntity\User\UserMetaEntity;

/**
 * Class UserMetaStorage
 * @package WebHemi\DataStorage\User
 */
class UserMetaStorage extends AbstractDataStorage
{
    /** @var string  */
    protected $dataGroup = 'user_meta';
    /** @var  string */
    protected $idKey = 'id_user_meta';

    /**
     * Returns a User Meta entity identified by ID.
     *
     * @param int $id
     * @return UserMetaEntity
     */
    public function getUserMetaById($id)
    {
        return $this->getEntityByIdKey($id);
    }

    /**
     * Returns all User Meta entity for a user identified by User ID.
     *
     * @param $userId
     * @return UserMetaEntity[]
     */
    public function getUserMeta($userId)
    {
        return $this->getEntityListByExpression(['fk_user' => $userId]);
    }
}
