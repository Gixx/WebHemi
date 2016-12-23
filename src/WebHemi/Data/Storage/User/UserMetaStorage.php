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
namespace WebHemi\Data\Storage\User;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Storage\AbstractDataStorage;
use WebHemi\Data\Storage\Traits\GetEntityListFromDataSetTrait;

/**
 * Class UserMetaStorage.
 */
class UserMetaStorage extends AbstractDataStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_user_meta';
    /** @var string */
    protected $idKey = 'id_user_meta';
    /** @var string */
    private $userId = 'fk_user';
    /** @var string */
    private $metaKey = 'meta_key';
    /** @var string */
    private $metaData = 'meta_data';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /** @method bool|array<UserMetaEntity> getEntityListFromDataSet(array $dataList) */
    use GetEntityListFromDataSetTrait;

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    protected function populateEntity(DataEntityInterface&$entity, array $data)
    {
        /* @var UserMetaEntity $entity */
        $entity->setUserMetaId($data[$this->idKey])
            ->setUserId($data[$this->userId])
            ->setMetaKey($data[$this->metaKey])
            ->setMetaData($data[$this->metaData])
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
        /** @var UserMetaEntity $entity */
        $dateCreated = $entity->getDateCreated();
        $dateModified = $entity->getDateModified();

        return [
            $this->idKey => $entity->getKeyData(),
            $this->userId => $entity->getUserId(),
            $this->metaKey => $entity->getMetaKey(),
            $this->metaData => $entity->getMetaData(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a User Meta entity identified by (unique) ID.
     *
     * @param int $identifier
     *
     * @return null|UserMetaEntity
     */
    public function getUserMetaById($identifier)
    {
        $entity = null;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
            /** @var UserMetaEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

        return $entity;
    }

    /**
     * Returns a User Meta entity list identified by user ID.
     *
     * @param mixed $userId
     *
     * @return array<UserMetaEntity>
     */
    public function getUserMetaForUserId($userId)
    {
        $dataList = $this->getDataAdapter()->getDataSet([$this->userId => $userId]);

        return $this->getEntityListFromDataSet($dataList);
    }
}
