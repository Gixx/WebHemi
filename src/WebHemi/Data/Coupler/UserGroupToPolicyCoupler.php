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

namespace WebHemi\Data\Coupler;

use WebHemi\Data\Traits\PolicyEntityTrait;
use WebHemi\Data\Traits\UserGroupEntityTrait;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class UserToPolicyCoupler.
 */
class UserGroupToPolicyCoupler extends AbstractCoupler
{
    /** @var string */
    protected $connectorIdKey = 'id_user_group_to_am_policy';
    /** @var string */
    protected $connectorDataGroup = 'webhemi_user_group_to_am_policy';
    /** @var array */
    protected $dependentDataGroups = [
        UserGroupEntity::class => [
            'source_key' => 'fk_user_group',
            'connector_key' => 'fk_am_policy',
            'depending_group' => 'webhemi_am_policy',
            'depending_id_key' => 'id_am_policy',
        ],
        PolicyEntity::class => [
            'source_key' => 'fk_am_policy',
            'connector_key' => 'fk_user_group',
            'depending_group' => 'webhemi_user_group',
            'depending_id_key' => 'id_user_group',
        ]
    ];

    use UserGroupEntityTrait;
    use PolicyEntityTrait;

    /**
     * Gets an EntityInterface instance from the provided data according to the reference entity.
     *
     * @param EntityInterface $referenceEntity
     * @param array           $entityData
     * @return EntityInterface
     */
    protected function getDependingEntity(EntityInterface $referenceEntity, array $entityData) : EntityInterface
    {
        return $referenceEntity instanceof UserGroupEntity
            ? $this->createPolicyEntity($entityData)
            : $this->createUserGroupEntity($entityData);
    }
}
