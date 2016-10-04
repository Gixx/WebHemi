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
namespace WebHemi\DataStorage\User;

use DateTime;
use WebHemi\DataEntity\DataEntityInterface;
use WebHemi\DataEntity\User\UserGroupEntity;
use WebHemi\DataStorage\AbstractDataStorage;

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
    private $title = 'title';
    /** @var string */
    private $description = 'description';
    /** @var int */
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
    protected function populateEntity(DataEntityInterface &$entity, array $data)
    {
        /* @var UserGroupEntity $entity */
        $entity->setUserGroupId($data[$this->idKey])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setReadOnly($data[$this->isReadOnly])
            ->setDateCreated(new DateTime($data[$this->dateCreated]))
            ->setDateModified(new DateTime($data[$this->dateModified]));
    }

    /**
     * Returns a User entity identified by (unique) ID.
     *
     * @param int $identifier
     *
     * @return bool|UserGroupEntity
     */
    public function getUserGroupById($identifier)
    {
        $entity = false;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

        return $entity;
    }
}
