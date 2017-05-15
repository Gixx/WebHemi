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
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Storage\AbstractStorage;

/**
 * Class UserGroupStorage.
 */
class UserGroupStorage extends AbstractStorage
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
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @return void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var UserGroupEntity $dataEntity */
        $dataEntity->setUserGroupId((int) $data[$this->idKey])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
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
        /** @var UserGroupEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->name => $dataEntity->getName(),
            $this->title => $dataEntity->getTitle(),
            $this->description => $dataEntity->getDescription(),
            $this->isReadOnly => (int) $dataEntity->getReadOnly(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a full set of User Group entities.
     *
     * @return null|array
     */
    public function getUserGroups() : ? array
    {
        return $this->getDataEntitySet([]);
    }

    /**
     * Returns a User Group entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|UserGroupEntity
     */
    public function getUserGroupById(int $identifier) : ? UserGroupEntity
    {
        /** @var null|UserGroupEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Returns a User Group entity by name.
     *
     * @param string $name
     * @return null|UserGroupEntity
     */
    public function getUserGroupByName(string $name) : ? UserGroupEntity
    {
        /** @var null|UserGroupEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->name => $name]);

        return $dataEntity;
    }
}
