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

use WebHemi\Data\Coupler\Traits\UserEntityTrait;
use WebHemi\Data\Coupler\Traits\UserGroupEntityTrait;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserGroupEntity;

/**
 * Class UserToPolicyCoupler.
 */
class UserToGroupCoupler extends AbstractDataCoupler
{
    /** @var string */
    protected $connectorIdKey = 'id_user_to_user_group';
    /** @var string */
    protected $connectorDataGroup = 'webhemi_user_to_user_group';
    /** @var array */
    protected $dependentDataGroups = [
        UserEntity::class => [
            'source_key' => 'fk_user',
            'connector_key' => 'fk_user_group',
            'depending_group' => 'webhemi_user_group',
            'depending_id_key' => 'id_user_group',
        ],
        UserGroupEntity::class => [
            'source_key' => 'fk_user_group',
            'connector_key' => 'fk_user',
            'depending_group' => 'webhemi_user',
            'depending_id_key' => 'id_user',
        ]
    ];

    use UserEntityTrait;
    use UserGroupEntityTrait;

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param DataEntityInterface $referenceEntity
     * @param array               $entityData
     * @return DataEntityInterface
     */
    protected function getDependingEntity(DataEntityInterface $referenceEntity, array $entityData) : DataEntityInterface
    {
        return $referenceEntity instanceof UserEntity
            ? $this->createUserGroupEntity($entityData)
            : $this->createUserEntity($entityData);
    }
}
