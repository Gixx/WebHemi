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

namespace WebHemi\Data\Storage\User;

use WebHemi\DateTime;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\AbstractStorage;

/**
 * Class UserStorage.
 */
class UserStorage extends AbstractStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_user';
    /** @var string */
    protected $idKey = 'id_user';
    /** @var string */
    private $userName = 'username';
    /** @var string */
    private $email = 'email';
    /** @var string */
    private $password = 'password';
    /** @var string */
    private $hash = 'hash';
    /** @var string */
    private $isActive = 'is_active';
    /** @var string */
    private $isEnabled = 'is_enabled';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @return void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var UserEntity $dataEntity */
        $dataEntity->setUserId((int) $data[$this->idKey])
            ->setUserName($data[$this->userName])
            ->setEmail($data[$this->email])
            ->setPassword($data[$this->password])
            ->setHash($data[$this->hash])
            ->setActive((bool) $data[$this->isActive])
            ->setEnabled((bool) $data[$this->isEnabled])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(new DateTime($data[$this->dateModified] ?? 'now'));
    }

    /**
     * Get data from an entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /** @var UserEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->userName => $dataEntity->getUserName(),
            $this->email => $dataEntity->getEmail(),
            $this->password => $dataEntity->getPassword(),
            $this->hash => $dataEntity->getHash(),
            $this->isActive => (int) $dataEntity->getActive(),
            $this->isEnabled => (int) $dataEntity->getEnabled(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a User entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|UserEntity
     */
    public function getUserById(int $identifier) : ? UserEntity
    {
        /** @var null|UserEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Returns a User entity by user name.
     *
     * @param string $name
     * @return null|UserEntity
     */
    public function getUserByUserName(string $name) : ? UserEntity
    {
        /** @var null|UserEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->userName => $name]);

        return $dataEntity;
    }

    /**
     * Returns a User entity by email.
     *
     * @param string $email
     * @return null|UserEntity
     */
    public function getUserByEmail($email) : ? UserEntity
    {
        /** @var null|UserEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->email => $email]);

        return $dataEntity;
    }

    /**
     * Return a User entity by credentials.
     *
     * @param string $username
     * @param string $password
     * @return null|UserEntity
     */
    public function getUserByCredentials(string $username, string $password) : ? UserEntity
    {
        /** @var null|UserEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->userName => $username, $this->password => $password]);

        return $dataEntity;
    }
}
