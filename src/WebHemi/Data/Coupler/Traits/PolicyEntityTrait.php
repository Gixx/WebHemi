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
namespace WebHemi\Data\Coupler\Traits;

use WebHemi\DateTime;
use RuntimeException;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class PolicyEntityTrait.
 */
trait PolicyEntityTrait
{
    /**
     * Returns a new instance of the required entity.
     *
     * @param string $entityClassName
     * @throws RuntimeException
     * @return PolicyEntity
     */
    abstract protected function getNewEntityInstance($entityClassName);

    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return PolicyEntity
     */
    protected function createPolicyEntity(array $data)
    {
        $entity = $this->getNewEntityInstance(PolicyEntity::class);

        $entity->setPolicyId($data['id_am_policy'])
            ->setResourceId($data['fk_am_resource'])
            ->setApplicationId($data['fk_application'])
            ->setName($data['name'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setReadOnly($data['is_read_only'])
            ->setAllowed($data['is_allowed'])
            ->setDateCreated(new DateTime($data['date_created']))
            ->setDateModified(new DateTime($data['date_created']));

        return $entity;
    }
}
