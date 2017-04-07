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
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Storage\AbstractStorage;

/**
 * Class PolicyStorage.
 */
class PolicyStorage extends AbstractStorage
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
    private $method = 'method';
    /** @var string */
    private $isReadOnly = 'is_read_only';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @param void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var PolicyEntity $dataEntity */
        $dataEntity->setPolicyId((int) $data[$this->idKey])
            ->setResourceId(!empty($data[$this->resourceId]) ? (int) $data[$this->resourceId] : null)
            ->setApplicationId(!empty($data[$this->applicationId]) ? (int) $data[$this->applicationId] : null)
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setMethod($data[$this->method])
            ->setReadOnly((bool) $data[$this->isReadOnly])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(new DateTime($data[$this->dateModified] ?? 'now'));
    }

    /**
     * Get data from an entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /** @var PolicyEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->resourceId => $dataEntity->getResourceId(),
            $this->applicationId => $dataEntity->getApplicationId(),
            $this->name => $dataEntity->getName(),
            $this->title => $dataEntity->getTitle(),
            $this->description => $dataEntity->getDescription(),
            $this->method => $dataEntity->getMethod(),
            $this->isReadOnly => (int) $dataEntity->getReadOnly(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a full set of Policy entities.
     *
     * @return null|array
     */
    public function getPolicies() : ? array
    {
        return $this->getDataEntitySet([]);
    }

    /**
     * Returns a Policy entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|PolicyEntity
     */
    public function getPolicyById($identifier) : ? PolicyEntity
    {
        /** @var null|PolicyEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Returns a Policy entity by name.
     *
     * @param string $name
     * @return null|PolicyEntity
     */
    public function getPolicyByName($name) : ? PolicyEntity
    {
        /** @var null|PolicyEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->name => $name]);

        return $dataEntity;
    }

    /**
     * Returns a set of Policy entities identified by Resource ID.
     *
     * @param int $resourceId
     * @return PolicyEntity[]
     */
    public function getPoliciesByResourceId($resourceId) : array
    {
        return $this->getDataEntitySet([$this->resourceId => $resourceId]);
    }

    /**
     * Returns a set of Policy entities identified by Application ID.
     *
     * @param int $applicationId
     * @return PolicyEntity[]
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
     * @return PolicyEntity[]
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
