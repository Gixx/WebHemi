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

namespace WebHemi\Data\Coupler\Traits;

use WebHemi\DateTime;
use RuntimeException;
use WebHemi\Data\EntityInterface;
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
     * @return EntityInterface
     */
    abstract protected function getNewEntityInstance(string $entityClassName) : EntityInterface;

    /**
     * Creates a new Policy Entity instance form the data.
     *
     * @param array $data
     * @return PolicyEntity
     */
    protected function createPolicyEntity(array $data) : PolicyEntity
    {
        /** @var PolicyEntity $entity */
        $entity = $this->getNewEntityInstance(PolicyEntity::class);

        $entity->setPolicyId((int) $data['id_am_policy'])
            ->setResourceId(!empty($data['fk_am_resource']) ? (int) $data['fk_am_resource'] : null)
            ->setApplicationId(!empty($data['fk_application']) ? (int) $data['fk_application'] : null)
            ->setName($data['name'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setMethod($data['method'])
            ->setReadOnly((bool) $data['is_read_only'])
            ->setAllowed((bool) $data['is_allowed'])
            ->setDateCreated(new DateTime($data['date_created'] ?? 'now'))
            ->setDateModified(new DateTime($data['date_created'] ?? 'now'));

        return $entity;
    }
}
