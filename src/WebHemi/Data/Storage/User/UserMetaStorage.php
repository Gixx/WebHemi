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

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     */
    protected function populateEntity(DataEntityInterface &$entity, array $data)
    {
        /* @var UserMetaEntity $entity */
        $entity->setUserMetaId($data[$this->idKey])
            ->setUserId($data[$this->userId])
            ->setMetaKey($data[$this->metaKey])
            ->setMetaData($data[$this->metaData]);
    }

    /**
     * Returns a User Meta entity identified by (unique) ID.
     *
     * @param int $identifier
     *
     * @return bool|UserMetaEntity
     */
    public function getUserMetaById($identifier)
    {
        $entity = false;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
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
        $entityList = false;
        $dataList = $this->getDataAdapter()->getDataSet([$this->userId => $userId]);

        if (!empty($dataList)) {
            foreach ($dataList as $metaData) {
                /** @var UserMetaEntity $entity */
                $entity = $this->createEntity();
                $this->populateEntity($entity, $metaData);
                $entityList[] = $entity;
            }
        }

        return $entityList;
    }
}
