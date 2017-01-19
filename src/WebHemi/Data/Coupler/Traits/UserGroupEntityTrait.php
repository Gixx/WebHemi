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

namespace WebHemi\Data\Coupler\Traits;

use WebHemi\DateTime;
use RuntimeException;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;

/**
 * Class UserGroupEntityTrait.
 */
trait UserGroupEntityTrait
{
    /**
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return DataEntityInterface
     */
    abstract protected function getNewEntityInstance(string $entityClassName) : DataEntityInterface;

    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return UserGroupEntity
     */
    protected function createUserGroupEntity(array $data) : UserGroupEntity
    {
        /** @var UserGroupEntity $entity */
        $entity = $this->getNewEntityInstance(UserGroupEntity::class);

        $entity->setUserGroupId((int) $data['id_user_group'])
            ->setName($data['name'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setReadOnly((bool) $data['is_read_only'])
            ->setDateCreated(new DateTime($data['date_created'] ?? 'now'))
            ->setDateModified(new DateTime($data['date_created'] ?? 'now'));

        return $entity;
    }
}
