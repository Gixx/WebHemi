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

use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\DateTime;
use WebHemi\StringLib;

/**
 * Class UserMetaStorage.
 */
class UserMetaStorage extends AbstractStorage
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
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @return void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var UserMetaEntity $dataEntity */
        $dataEntity->setUserMetaId((int) $data[$this->idKey])
            ->setUserId((int) $data[$this->userId])
            ->setMetaKey($data[$this->metaKey])
            ->setMetaData($data[$this->metaData])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(!empty($data[$this->dateModified]) ? new DateTime($data[$this->dateModified]) : null);
    }

    /**
     * Get data from an entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /** @var UserMetaEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->userId => $dataEntity->getUserId(),
            $this->metaKey => $dataEntity->getMetaKey(),
            $this->metaData => $dataEntity->getMetaData(),
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
    public function getUserMetaById(int $identifier) : ? UserMetaEntity
    {
        /** @var null|UserMetaEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Returns a User Meta data list identified by user ID.
     *
     * @param int $userId
     * @return array
     */
    public function getUserMetaArrayForUserId(int $userId) : array
    {
        $userMetaEntitySet = $this->getDataEntitySet([$this->userId => $userId]);
        $userMetaSet = [];

        /** @var UserMetaEntity $metaEntity */
        foreach ($userMetaEntitySet as $metaEntity) {
            $data = $this->processMetaEntity($metaEntity);
            $userMetaSet[$data['key']] = $data['value'];
        }

        return $userMetaSet;
    }

    /**
     * Processes a user meta entity.
     *
     * @param UserMetaEntity $metaEntity
     * @return array
     */
    private function processMetaEntity(UserMetaEntity $metaEntity) : array
    {

        $key = $metaEntity->getMetaKey();
        $data = $metaEntity->getMetaData();

        if ($key == 'avatar' && strpos($data, 'gravatar://') === 0) {
            $data = str_replace('gravatar://', '', $data);
            $data = 'http://www.gravatar.com/avatar/'.md5(strtolower($data)).'?s=256&r=g';
        }

        $jsonDataKeys = ['workplaces', 'instant_messengers', 'phone_numbers', 'social_networks', 'websites'];

        if (in_array($key, $jsonDataKeys) && !empty($data)) {
            $data = json_decode($data, true);
        }

        $data = [
            'key' => lcfirst(StringLib::convertUnderscoreToCamelCase($key)),
            'value' => $data
        ];

        return $data;
    }

    /**
     * Returns a User Meta entity list identified by user ID.
     *
     * @param int  $userId
     * @param bool $keysAsKeys -  whether to use the meta keys for the returning array too
     * @return UserMetaEntity[]
     */
    public function getUserMetaEntitySetForUserId(int $userId, bool $keysAsKeys = false) : array
    {
        $metaSet = $this->getDataEntitySet([$this->userId => $userId]);

        if ($keysAsKeys) {
            $tmp = [];
            /** @var UserMetaEntity $userMetaEntity */
            foreach ($metaSet as $userMetaEntity) {
                $tmp[$userMetaEntity->getMetaKey()] = $userMetaEntity;
            }
            $metaSet = $tmp;
            unset($tmp);
        }

        return $metaSet;
    }
}
