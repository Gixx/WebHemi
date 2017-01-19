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

namespace WebHemi\Data\Storage\User;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Storage\AbstractDataStorage;

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

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     * @return void
     */
    protected function populateEntity(DataEntityInterface&$entity, array $data) : void
    {
        /* @var UserMetaEntity $entity */
        $entity->setUserMetaId((int) $data[$this->idKey])
            ->setUserId((int) $data[$this->userId])
            ->setMetaKey($data[$this->metaKey])
            ->setMetaData($data[$this->metaData])
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
     * @return null|UserMetaEntity
     */
    public function getUserMetaById($identifier) : ? UserMetaEntity
    {
        /** @var null|UserMetaEntity $entity */
        $entity = $this->getDataEntity([$this->idKey => $identifier]);

        return $entity;
    }

    /**
     * Returns a User Meta entity list identified by user ID.
     *
     * @param mixed $userId
     * @return array<UserMetaEntity>
     */
    public function getUserMetaForUserId($userId) : array
    {
        return $this->getDataEntitySet([$this->userId => $userId]);
    }
}
