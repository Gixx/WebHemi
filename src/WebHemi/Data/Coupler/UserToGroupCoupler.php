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
use WebHemi\Adapter\Data\DataAdapterInterface;
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
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return DataEntityInterface
     */
    protected function getNewEntityInstance($entityClassName)
    {
        if (!isset($this->dataEntityPrototypes[$entityClassName])) {
            throw new RuntimeException(sprintf('Class %s is not defined in this Coupler.', $entityClassName));
        }

        return clone $this->dataEntityPrototypes[$entityClassName];
    }

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
            $entityList[] = $entity instanceof UserEntity
                ? $this->createUserGroupEntity($entityData)
                : $this->createUserEntity($entityData);
        }

        return $entityList;
    }
}
