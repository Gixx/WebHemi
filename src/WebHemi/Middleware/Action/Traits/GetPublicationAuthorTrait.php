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

namespace WebHemi\Middleware\Action\Traits;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Router\ProxyInterface;
use WebHemi\Data\Traits\StorageInjectorTrait;

/**
 * Trait GetPublicationAuthorTrait
 */
trait GetPublicationAuthorTrait
{
    /**
     * @return null|Storage\User\UserStorage
     */
    abstract protected function getUserStorage() : ? Storage\User\UserStorage;

    /**
     * @return null|Storage\User\UserMetaStorage
     */
    abstract protected function getUserMetaStorage() : ? Storage\User\UserMetaStorage;

    /**
     * @return null|Storage\Filesystem\FilesystemDirectoryStorage
     */
    abstract protected function getFilesystemDirectoryStorage() : ? Storage\Filesystem\FilesystemDirectoryStorage;

    /**
     * Gets author information for a filesystem record.
     *
     * @param int $userId
     * @param int $applicationId
     * @return array
     */
    protected function getPublicationAuthor(int $userId, int $applicationId) : array
    {
        /** @var Entity\User\UserEntity $user */
        $user = $this->getUserStorage()
            ->getUserById($userId);

        /** @var array $userMeta */
        $userMeta = $this->getUserMetaStorage()
            ->getUserMetaSetForUserId($userId);

        /** @var array $userDirectoryData */
        $userDirectoryData = $this->getFilesystemDirectoryStorage()
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_USER);

        return [
            'user_id' => $userId,
            'username' => $user->getUserName(),
            'url' => $userDirectoryData['uri'].'/'.$user->getUserName(),
            'name' => $userMeta['display_name'] ?? $user->getUserName(),
            'email' => ($userMeta['email_visible'] ?? 0) ? $user->getEmail() : '',
            'avatar' => ($userMeta['avatar_type'] ?? '') == 'gravatar'
                ? 'http://www.gravatar.com/avatar/'.md5(strtolower($userMeta['avatar'])).'?s=256&r=g'
                : $userMeta['avatar'] ?? ''
        ];
    }
}
