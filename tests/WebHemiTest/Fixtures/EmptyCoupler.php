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
namespace WebHemiTest\Fixtures;

use WebHemi\Data\Coupler\AbstractDataCoupler;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class EmptyCoupler.
 */
class EmptyCoupler extends AbstractDataCoupler
{
    /** @var string */
    protected $connectorIdKey = '';
    /** @var string */
    protected $connectorDataGroup = '';
    /** @var array */
    protected $dependentDataGroups = [
        EmptyEntity::class => [
            'source_key' => '',
            'connector_key' => '',
            'depending_group' => '',
            'depending_id_key' => '',
        ],
    ];

    /**
     * Gets a DataEntityInterface instance from the provided data according to the reference entity.
     *
     * @param DataEntityInterface $referenceEntity
     * @param array               $entityData
     * @return DataEntityInterface
     */
    protected function getDependingEntity(DataEntityInterface $referenceEntity, array $entityData)
    {
        $entity = $this->getNewEntityInstance(EmptyEntity::class);

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
    protected function getEntityDataSet(DataEntityInterface $entity)
    {
        if ($entity) {
            return [
                [
                    'empty_id' => 1,
                    'title' => 'Some depending data 1',
                    'description' => 'Some data for the entity 1.'
                ],
                [
                    'empty_id' => 2,
                    'title' => 'Some depending data 2',
                    'description' => 'Some data for the entity 2.'
                ],
                [
                    'empty_id' => 2,
                    'title' => 'Some depending data 2',
                    'description' => 'Some data for the entity 2.'
                ],
            ];
        }

        return [];
    }
}
