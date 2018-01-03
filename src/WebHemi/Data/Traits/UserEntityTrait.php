<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Traits;

use WebHemi\DateTime;
use RuntimeException;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class UserEntityTrait.
 */
trait UserEntityTrait
{
    /**
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return EntityInterface
     */
    abstract protected function getNewEntityInstance(string $entityClassName) : EntityInterface;

    /**
     * Creates a new User Entity instance form the data.
     *
     * @param array $data
     * @return UserEntity
     */
    protected function createUserEntity(array $data) : UserEntity
    {
        /** @var UserEntity $entity */
        $entity = $this->getNewEntityInstance(UserEntity::class);

        $entity->setUserId((int) $data['id_user'])
            ->setUserName($data['username'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setHash($data['hash'])
            ->setActive((bool) $data['is_active'])
            ->setEnabled((bool) $data['is_enabled'])
            ->setDateCreated(new DateTime($data['date_created'] ?? 'now'))
            ->setDateModified(new DateTime($data['date_modified'] ?? 'now'));

        return $entity;
    }
}
