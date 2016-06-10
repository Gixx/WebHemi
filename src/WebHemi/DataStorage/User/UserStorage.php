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
     * @param int $identifier
     * @return bool|UserEntity
     */
    public function getUserById($identifier)
    {
        /** @var UserEntity $entity */
        $entity = $this->createEntity();
        $data = $this->getDataAdapter()->getData($identifier);

        // todo use the entity setters to fill with data
        $entity->setUserId($data['id_user']);

        return $entity;
    }

    /**
     * Returns a User entity identified by (unique) Email
     *
     * @param $email
     * @return bool|UserEntity
     */
    public function getUserByEmail($email)
    {
        $entity = false;
        $dataList = $this->getDataAdapter()->getDataSet(['email' => $email], 1);

        if ($dataList) {
            $entity = $this->getUserById($dataList[0]['user_id']);
        }

        return $entity;
    }
}
