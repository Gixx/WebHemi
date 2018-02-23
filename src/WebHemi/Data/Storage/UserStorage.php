<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Storage;

use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Data\Entity\UserGroupEntity;
use WebHemi\Data\Entity\UserMetaEntity;
use WebHemi\Data\Query\QueryInterface;
use WebHemi\StringLib;

/**
 * Class UserStorage.
 */
class UserStorage extends AbstractStorage
{
    /**
     * Returns a set of users.
     *
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getUserList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getUserList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(UserEntity::class, $data);
    }

    /**
     * Returns user information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|UserEntity
     */
    public function getUserById(int $identifier) : ? UserEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserById',
            [
                ':idUser' => $identifier
            ]
        );

        /** @var null|UserEntity $entity */
        $entity = $this->getEntity(UserEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns user information by user name.
     *
     * @param  string $username
     * @return null|UserEntity
     */
    public function getUserByUserName(string $username) : ? UserEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserByUsername',
            [
                ':username' => $username
            ]
        );

        /** @var null|UserEntity $entity */
        $entity = $this->getEntity(UserEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns user information by email.
     *
     * @param  string $email
     * @return null|UserEntity
     */
    public function getUserByEmail(string $email) : ? UserEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserByEmail',
            [
                ':email' => $email
            ]
        );

        /** @var null|UserEntity $entity */
        $entity = $this->getEntity(UserEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Return a user information by credentials.
     *
     * @param  string $username
     * @param  string $password
     * @return null|UserEntity
     */
    public function getUserByCredentials(string $username, string $password) : ? UserEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserByCredentials',
            [
                ':username' => $username,
                ':password' => $password
            ]
        );

        /** @var null|UserEntity $entity */
        $entity = $this->getEntity(UserEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns a user meta list identified by user ID.
     *
     * @param int $identifier
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getUserMetaListByUser(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getUserMetaListByUser',
            [
                ':userId' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(UserMetaEntity::class, $data);
    }

    /**
     * Returns a parsed/simplified form of the user meta list.
     *
     * @param int $identifier
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getSimpleUserMetaListByUser(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $metaInfo = [];
        $entitySet = $this->getUserMetaListByUser($identifier, $limit, $offset);

        foreach ($entitySet as $userMetaEntity) {
            $data = $this->processMetaData($userMetaEntity);
            $metaInfo[$data['key']] = $data['value'];
        }

        return $metaInfo;
    }

    /**
     * Processes a user meta information.
     *
     * @param UserMetaEntity $userMetaEntity
     * @return array
     */
    private function processMetaData(UserMetaEntity $userMetaEntity) : array
    {
        $key = $userMetaEntity->getMetaKey();
        $value = $userMetaEntity->getMetaData();

        if ($key == 'avatar' && strpos($value, 'gravatar://') === 0) {
            $value = str_replace('gravatar://', '', $value);
            $value = 'http://www.gravatar.com/avatar/'.md5(strtolower($value)).'?s=256&r=g';
        }

        $jsonDataKeys = ['workplaces', 'instant_messengers', 'phone_numbers', 'social_networks', 'websites'];

        if (in_array($key, $jsonDataKeys) && !empty($value)) {
            $value = json_decode($value, true);
        }

        $data = [
            'key' => lcfirst(StringLib::convertUnderscoreToCamelCase($key)),
            'value' => $value
        ];

        return $data;
    }

    /**
     * Returns a set of user groups.
     *
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getUserGroupList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getUserGroupList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(UserGroupEntity::class, $data);
    }

    /**
     * Returns a set of user groups.
     *
     * @param int $identifier
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getUserGroupListByUser(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getUserGroupListByUser',
            [
                ':userId' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(UserGroupEntity::class, $data);
    }

    /**
     * Returns user group information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|UserGroupEntity
     */
    public function getUserGroupById(int $identifier) : ? UserGroupEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserGroupById',
            [
                ':idUserGroup' => $identifier
            ]
        );

        /** @var null|UserGroupEntity $entity */
        $entity = $this->getEntity(UserGroupEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns a user group information by name.
     *
     * @param  string $name
     * @return null|UserGroupEntity
     */
    public function getUserGroupByName(string $name) : ? UserGroupEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getUserGroupByName',
            [
                ':name' => $name
            ]
        );

        /** @var null|UserGroupEntity $entity */
        $entity = $this->getEntity(UserGroupEntity::class, $data[0] ?? []);

        return $entity;
    }
}
