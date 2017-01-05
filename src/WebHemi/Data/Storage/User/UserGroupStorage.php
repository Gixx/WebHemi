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
namespace WebHemi\Data\Storage\User;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Storage\AbstractDataStorage;

/**
 * Class UserGroupStorage.
 */
class UserGroupStorage extends AbstractDataStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_user_group';
    /** @var string */
    protected $idKey = 'id_user_group';
    /** @var string */
    private $name = 'name';
    /** @var string */
    private $title = 'title';
    /** @var string */
    private $description = 'description';
    /** @var string */
    private $isReadOnly = 'is_read_only';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    protected function populateEntity(DataEntityInterface&$entity, array $data)
    {
        /* @var UserGroupEntity $entity */
        $entity->setUserGroupId($data[$this->idKey])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setReadOnly($data[$this->isReadOnly])
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
        /** @var UserGroupEntity $entity */
        $dateCreated = $entity->getDateCreated();
        $dateModified = $entity->getDateModified();

        return [
            $this->idKey => $entity->getKeyData(),
            $this->name => $entity->getName(),
            $this->title => $entity->getTitle(),
            $this->description => $entity->getDescription(),
            $this->isReadOnly => (int) $entity->getReadOnly(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a User Group entity identified by (unique) ID.
     *
     * @param int $identifier
     *
     * @return null|UserGroupEntity
     */
    public function getUserGroupById($identifier)
    {
        $entity = null;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
            /** @var UserGroupEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

        return $entity;
    }

    /**
     * Returns a User Group entity by name.
     *
     * @param string $name
     *
     * @return null|UserGroupEntity
     */
    public function getUserGroupByName($name)
    {
        $entity = null;
        $dataList = $this->getDataAdapter()->getDataSet([$this->name => $name], 1);

        if (!empty($dataList)) {
            /** @var UserGroupEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $dataList[0]);
        }

        return $entity;
    }
}
