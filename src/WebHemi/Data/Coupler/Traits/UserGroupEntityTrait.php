<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Data\Coupler\Traits;

use DateTime;
use RuntimeException;
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
     * @return UserGroupEntity
     */
    abstract protected function getNewEntityInstance($entityClassName);

    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return UserGroupEntity
     */
    protected function createUserGroupEntity(array $data)
    {
        $entity = $this->getNewEntityInstance(UserGroupEntity::class);

        $entity->setUserGroupId($data['id_user_group'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setReadOnly($data['is_read_only'])
            ->setDateCreated(new DateTime($data['date_created']))
            ->setDateModified(new DateTime($data['date_created']));

        return $entity;
    }
}
