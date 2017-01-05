<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Data\Coupler;

use WebHemi\Data\Coupler\Traits\PolicyEntityTrait;
use WebHemi\Data\Coupler\Traits\UserEntityTrait;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class UserToPolicyCoupler.
 */
class UserToPolicyCoupler extends AbstractDataCoupler
{
    /** @var string */
    protected $connectorIdKey = 'id_user_to_am_policy';
    /** @var string */
    protected $connectorDataGroup = 'webhemi_user_to_am_policy';
    /** @var array */
    protected $dependentDataGroups = [
        UserEntity::class => [
            'source_key' => 'fk_user',
            'connector_key' => 'fk_am_policy',
            'depending_group' => 'webhemi_am_policy',
            'depending_id_key' => 'id_am_policy',
        ],
        PolicyEntity::class => [
            'source_key' => 'fk_am_policy',
            'connector_key' => 'fk_user',
            'depending_group' => 'webhemi_user',
            'depending_id_key' => 'id_user',
        ]
    ];

    use UserEntityTrait;
    use PolicyEntityTrait;

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param DataEntityInterface $referenceEntity
     * @param array               $entityData
     * @return DataEntityInterface
     */
    protected function getDependingEntity(DataEntityInterface $referenceEntity, array $entityData)
    {
        return $referenceEntity instanceof UserEntity
            ? $this->createPolicyEntity($entityData)
            : $this->createUserEntity($entityData);
    }
}
