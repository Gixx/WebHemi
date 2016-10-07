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
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class PolicyEntityTrait.
 */
trait PolicyEntityTrait
{
    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return PolicyEntity
     */
    protected function createPolicyEntity(array $data)
    {
        /* @var PolicyEntity $entity */
        $entity = parent::getNewEntityInstance(PolicyEntity::class);

        $entity->setPolicyId($data['id_am_policy'])
            ->setResourceId($data['fk_am_resource'])
            ->setApplicationId($data['fk_application'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setReadOnly($data['is_read_only'])
            ->setAllowed($data['is_allowed'])
            ->setDateCreated(new DateTime($data['date_created']))
            ->setDateModified(new DateTime($data['date_created']));

        return $entity;
    }
}
