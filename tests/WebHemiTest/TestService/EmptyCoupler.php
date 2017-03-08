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
namespace WebHemiTest\TestService;

use WebHemi\Data\Coupler\AbstractCoupler;
use WebHemi\Data\EntityInterface as DataEntityInterface;

/**
 * Class EmptyCoupler.
 */
class EmptyCoupler extends AbstractCoupler
{
    /** @var string */
    protected $connectorIdKey = 'id_empty_to_empty2';
    /** @var string */
    protected $connectorDataGroup = 'empty_to_empty2';
    /** @var array */
    protected $dependentDataGroups = [
        EmptyEntity::class => [
            'source_key' => 'empty_fk_1',
            'connector_key' => 'empty_fk_2',
            'depending_group' => 'group2',
            'depending_id_key' => 'empty_id_2',
        ],
        EmptyEntity2::class => [
            'source_key' => 'empty_fk_2',
            'connector_key' => 'empty_fk_1',
            'depending_group' => 'group1',
            'depending_id_key' => 'empty_id_1',
        ],
    ];

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param DataEntityInterface $referenceEntity
     * @param array               $entityData
     * @return DataEntityInterface
     */
    protected function getDependingEntity(DataEntityInterface $referenceEntity, array $entityData) : DataEntityInterface
    {
        $entityClass = get_class($referenceEntity);
        $entity = $this->getNewEntityInstance($entityClass);

        if ($referenceEntity) {
            foreach ($entityData as $key => $value) {
                $method = 'set'.ucfirst($key);

                $entity->{$method}($value);
            }
        }

        return $entity;
    }

    /**
     * Gets raw depending entity data list for the given entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    protected function getEntityDataSet(DataEntityInterface $entity) : array
    {
        if ($entity instanceof EmptyEntity2) {
            return [
                [
                    'empty_id_2' => 1,
                    's_key2' => 1,
                    'title' => 'Some depending data 4',
                    'description' => 'Some data for the entity 4.'
                ],
                [
                    'empty_id_2' => 2,
                    'title' => 'Some depending data 5',
                    'description' => 'Some data for the entity 5.'
                ],
            ];
        } elseif ($entity instanceof EmptyEntity) {
            return [
                [
                    'empty_id_1' => 1,
                    'title' => 'Some depending data 1',
                    'description' => 'Some data for the entity 1.'
                ],
                [
                    'empty_id_1' => 2,
                    'title' => 'Some depending data 2',
                    'description' => 'Some data for the entity 2.'
                ],
                [
                    'empty_id_1' => 3,
                    'title' => 'Some depending data 3',
                    'description' => 'Some data for the entity 3.'
                ],
            ];
        }
        return [];
    }
}
