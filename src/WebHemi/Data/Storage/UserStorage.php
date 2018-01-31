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
     * @return null|array
     */
    public function getUserList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $users = null;

        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getUserList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $users[$row['email']] = $row;
        }

        return $users;
    }

    /**
     * Returns user information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getUserById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData('getUserById', [':idUser' => $identifier]);

        return $data[0] ?? null;
    }

    /**
     * Returns user information by user name.
     *
     * @param  string $username
     * @return null|array
     */
    public function getUserByUsername(string $username) : ? array
    {
        $data = $this->queryAdapter->fetchData('getUserByUsername', [':username' => $username]);

        return $data[0] ?? null;
    }

    /**
     * Returns user information by email.
     *
     * @param  string $email
     * @return null|array
     */
    public function getUserByEmail(string $email) : ? array
    {
        $data = $this->queryAdapter->fetchData('getUserByEmail', [':email' => $email]);

        return $data[0] ?? null;
    }

    /**
     * Return a user information by credentials.
     *
     * @param  string $username
     * @param  string $password
     * @return null|array
     */
    public function getUserByCredentials(string $username, string $password) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getUserByCredentials',
            [
                ':username' => $username,
                ':password' => $password
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns a user meta list identified by user ID.
     *
     * @param int $identifier
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUserMetaListByUser(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $list = [];
        $this->normalizeLimitAndOffset($limit, $offset);

        $metaDataList = $this->queryAdapter->fetchData(
            'getUserMetaListByUser',
            [
                ':userId' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($metaDataList as $metaData) {
            $data = $this->processMetaData($metaData['meta_key'], $metaData['meta_data']);
            $list[$data['key']] = $data['value'];
        }

        return $list;
    }

    /**
     * Processes a user meta information.
     *
     * @param string $key
     * @param string $value
     * @return array
     */
    private function processMetaData(string $key, string $value) : array
    {
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
     * @return null|array
     */
    public function getUserGroupList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $userGroups = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getUserGroupList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $userGroups[$row['name']] = $row;
        }

        return $userGroups;
    }

    /**
     * Returns a set of user groups.
     *
     * @param int $identifier
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getUserGroupListByUser(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $userGroups = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getUserGroupListByUser',
            [
                ':userId' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $userGroups[$row['name']] = $row;
        }

        return $userGroups;
    }

    /**
     * Returns user group information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getUserGroupById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData('getUserGroupById', [':idUserGroup' => $identifier]);

        return $data[0] ?? null;
    }

    /**
     * Returns a user group information by name.
     *
     * @param  string $name
     * @return null|array
     */
    public function getUserGroupByName(string $name) : ? array
    {
        $data = $this->queryAdapter->fetchData('getUserGroupByName', [':name' => $name]);

        return $data[0] ?? null;
    }
}
