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

namespace WebHemi\Data\Storage\AccessManagement;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Storage\AbstractDataStorage;

/**
 * Class PolicyStorage.
 */
class PolicyStorage extends AbstractDataStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_am_policy';
    /** @var string */
    protected $idKey = 'id_am_policy';
    /** @var string */
    private $resourceId = 'fk_am_resource';
    /** @var string */
    private $applicationId = 'fk_application';
    /** @var string */
    private $name = 'name';
    /** @var string */
    private $title = 'title';
    /** @var string */
    private $description = 'description';
    /** @var string */
    private $isReadOnly = 'is_read_only';
    /** @var string */
    private $isAllowed = 'is_allowed';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     * @param void
     */
    protected function populateEntity(DataEntityInterface&$entity, array $data) : void
    {
        /* @var PolicyEntity $entity */
        $entity->setPolicyId((int) $data[$this->idKey])
            ->setResourceId(!empty($data[$this->resourceId]) ? (int) $data[$this->resourceId] : null)
            ->setApplicationId(!empty($data[$this->applicationId]) ? (int) $data[$this->applicationId] : null)
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setReadOnly((bool) $data[$this->isReadOnly])
            ->setAllowed((bool) $data[$this->isAllowed])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(new DateTime($data[$this->dateModified] ?? 'now'));
    }

    /**
     * Get data from an entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    protected function getEntityData(DataEntityInterface $entity) : array
    {
        /** @var PolicyEntity $entity */
        $dateCreated = $entity->getDateCreated();
        $dateModified = $entity->getDateModified();

        return [
            $this->idKey => $entity->getKeyData(),
            $this->resourceId => $entity->getResourceId(),
            $this->applicationId => $entity->getApplicationId(),
            $this->name => $entity->getName(),
            $this->title => $entity->getTitle(),
            $this->description => $entity->getDescription(),
            $this->isReadOnly => (int) $entity->getReadOnly(),
            $this->isAllowed => (int) $entity->getAllowed(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a Policy entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|PolicyEntity
     */
    public function getPolicyById($identifier)
    {
        /** @var null|PolicyEntity $entity */
        $entity = $this->getDataEntity([$this->idKey => $identifier]);

        return $entity;
    }

    /**
     * Returns a Policy entity by name.
     *
     * @param string $name
     * @return null|PolicyEntity
     */
    public function getPolicyByName($name)
    {
        /** @var null|PolicyEntity $entity */
        $entity = $this->getDataEntity([$this->name => $name]);

        return $entity;
    }

    /**
     * Returns a set of Policy entities identified by Resource ID.
     *
     * @param int $resourceId
     * @return array<PolicyEntity>
     */
    public function getPoliciesByResourceId($resourceId) : array
    {
        return $this->getDataEntitySet([$this->resourceId => $resourceId]);
    }

    /**
     * Returns a set of Policy entities identified by Application ID.
     *
     * @param int $applicationId
     * @return array<PolicyEntity>
     */
    public function getPoliciesByApplicationId($applicationId) : array
    {
        return $this->getDataEntitySet([$this->applicationId => $applicationId]);
    }

    /**
     * Returns a set of Policy entities identified by both Resource and Application IDs.
     *
     * @param int $resourceId
     * @param int $applicationId
     * @return array<PolicyEntity>
     */
    public function getPoliciesByResourceAndApplicationIds($resourceId, $applicationId) : array
    {
        return $this->getDataEntitySet(
            [
                $this->resourceId => $resourceId,
                $this->applicationId => $applicationId
            ]
        );
    }
}
