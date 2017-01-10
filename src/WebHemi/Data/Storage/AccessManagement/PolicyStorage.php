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
namespace WebHemi\Data\Storage\AccessManagement;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Storage\AbstractDataStorage;
use WebHemi\Data\Storage\Traits\GetEntityListFromDataSetTrait;

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

    /** @method bool|array<PolicyEntity> getEntityListFromDataSet(array $dataList) */
    use GetEntityListFromDataSetTrait;

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    protected function populateEntity(DataEntityInterface&$entity, array $data)
    {
        /* @var PolicyEntity $entity */
        $entity->setPolicyId($data[$this->idKey])
            ->setResourceId($data[$this->resourceId])
            ->setApplicationId($data[$this->applicationId])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setReadOnly($data[$this->isReadOnly])
            ->setAllowed($data[$this->isAllowed])
            ->setDateCreated(new DateTime($data[$this->dateCreated]))
            ->setDateModified(new DateTime($data[$this->dateModified]));
    }

    /**
     * Get data from an entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    protected function getEntityData(DataEntityInterface $entity)
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
     *
     * @return null|PolicyEntity
     */
    public function getPolicyById($identifier)
    {
        $entity = null;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
            /** @var PolicyEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

        return $entity;
    }

    /**
     * Returns a Policy entity by name.
     *
     * @param string $name
     *
     * @return null|PolicyEntity
     */
    public function getPolicyByName($name)
    {
        $entity = null;
        $dataList = $this->getDataAdapter()->getDataSet([$this->name => $name], 1);

        if (!empty($dataList)) {
            /** @var PolicyEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $dataList[0]);
        }

        return $entity;
    }

    /**
     * Returns a set of Policy entities identified by Resource ID.
     *
     * @param int $resourceId
     *
     * @return bool|array<PolicyEntity>
     */
    public function getPoliciesByResourceId($resourceId)
    {
        $dataList = $this->getDataAdapter()->getDataSet([$this->resourceId => $resourceId]);

        return $this->getEntityListFromDataSet($dataList);
    }

    /**
     * Returns a set of Policy entities identified by Application ID.
     *
     * @param int $applicationId
     *
     * @return bool|array<PolicyEntity>
     */
    public function getPoliciesByApplicationId($applicationId)
    {
        $dataList = $this->getDataAdapter()->getDataSet([$this->applicationId => $applicationId]);

        return $this->getEntityListFromDataSet($dataList);
    }

    /**
     * Returns a set of Policy entities identified by both Resource and Application IDs.
     *
     * @param int $resourceId
     * @param int $applicationId
     *
     * @return bool|array<PolicyEntity>
     */
    public function getPoliciesByResourceAndApplicationIds($resourceId, $applicationId)
    {
        $dataList = $this->getDataAdapter()->getDataSet(
            [
                $this->resourceId => $resourceId,
                $this->applicationId => $applicationId
            ]
        );

        return $this->getEntityListFromDataSet($dataList);
    }
}
