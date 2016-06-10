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
use WebHemi\DataEntity\User\UserEntity;

/**
 * Class UserStorage
 * @package WebHemi\DataStorage\User
 */
class UserStorage extends AbstractDataStorage
{
    /** @var string  */
    protected $dataGroup = 'user';
    /** @var  string */
    protected $idKey = 'id_user';

    /**
     * Returns a User entity identified by (unique) ID
     *
     * @param int $id
     * @return bool|UserEntity
     */
    public function getUserById($id)
    {
        return $this->getEntityByIdKey($id);
    }

    /**
     * Returns a User entity identified by (unique) Email
     *
     * @param $email
     * @return bool|UserEntity
     */
    public function getUserByEmail($email)
    {
        $dataSet = $this->getEntityListByExpression(['email' => $email], 1);

        return isset($dataSet[0]) ? $dataSet[0] : false;
    }
}
