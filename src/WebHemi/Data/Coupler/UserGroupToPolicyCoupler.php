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
namespace WebHemi\Data\Coupler;

use RuntimeException;
use WebHemi\Data\Coupler\Traits\PolicyEntityTrait;
use WebHemi\Data\Coupler\Traits\UserGroupEntityTrait;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class UserToPolicyCoupler.
 */
class UserGroupToPolicyCoupler extends AbstractDataCoupler
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
     * Gets all the entities those are depending from the given entity.
     *
     * @param DataEntityInterface $entity
     * @throws RuntimeException
     * @return array<DataEntityInterface>
     */
    public function getEntityDependencies(DataEntityInterface $entity)
    {
        $entityClass = get_class($entity);
        if (!isset($this->dataEntityPrototypes[$entityClass])) {
            throw new RuntimeException(
                sprintf('Cannot use this coupler class to find dependencies for %s.', $entityClass)
            );
        }

        $entityList = [];
        $dataList = $this->getEntityDataSet($entity);

        foreach ($dataList as $entityData) {
            $entityList[] = $entity instanceof UserGroupEntity
                ? $this->createPolicyEntity($entityData)
                : $this->createUserGroupEntity($entityData);
        }

        return $entityList;
    }
}
