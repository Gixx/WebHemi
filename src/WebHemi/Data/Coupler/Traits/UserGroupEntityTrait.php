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
use WebHemi\Data\Entity\User\UserGroupEntity;

/**
 * Class UserGroupEntityTrait.
 */
trait UserGroupEntityTrait
{
    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return UserGroupEntity
     */
    protected function createUserGroupEntity(array $data)
    {
        /* @var UserGroupEntity $entity */
        $entity = parent::getNewEntityInstance(UserGroupEntity::class);

        $entity->setUserGroupId($data['id_user_group'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setReadOnly($data['is_read_only'])
            ->setDateCreated(new DateTime($data['date_created']))
            ->setDateModified(new DateTime($data['date_created']));

        return $entity;
    }
}
