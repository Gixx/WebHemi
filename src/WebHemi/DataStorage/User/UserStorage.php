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

use DateTime;
use WebHemi\DataEntity\DataEntityInterface;
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
    /** @var  string */
    private $userName = 'username';
    /** @var  string */
    private $email = 'email';
    /** @var  string */
    private $password = 'password';
    /** @var  string */
    private $hash = 'hash';
    /** @var  string */
    private $lastIp = 'last_ip';
    /** @var  string */
    private $registerIp = 'register_ip';
    /** @var  string */
    private $isActive = 'is_active';
    /** @var  string */
    private $isEnabled = 'is_enabled';
    /** @var  string */
    private $timeLogin = 'time_login';
    /** @var  string */
    private $timeRegister = 'time_register';

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array $data
     */
    protected function populateEntity(DataEntityInterface &$entity, array $data)
    {
        /** @var UserEntity $entity */
        $entity->setUserId($data[$this->idKey])
            ->setUserName($data[$this->userName])
            ->setEmail($data[$this->email])
            ->setPassword($data[$this->password])
            ->setHash($data[$this->hash])
            ->setLastIp($data[$this->lastIp])
            ->setRegisterIp($data[$this->registerIp])
            ->setActive($data[$this->isActive])
            ->setEnabled($data[$this->isEnabled])
            ->setTimeLogin(new DateTime($data[$this->timeLogin]))
            ->setTimeRegister(new DateTime($data[$this->timeRegister]));
    }

    /**
     * Returns a User entity identified by (unique) ID
     *
     * @param int $identifier
     * @return bool|UserEntity
     */
    public function getUserById($identifier)
    {
        $entity = false;
        $data = $this->getDataAdapter()->getData($identifier);

        if ($data) {
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

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
        $dataList = $this->getDataAdapter()->getDataSet([$this->email => $email], 1);

        if ($dataList) {
            $entity = $this->createEntity();
            $this->populateEntity($entity, $dataList[0]);
        }

        return $entity;
    }
}
