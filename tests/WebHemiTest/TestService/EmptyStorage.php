<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Data\Entity\EntityInterface as DataEntityInterface;
use WebHemi\Data\Storage\AbstractStorage as AbstractDataStorage;

/**
 * Class EmptyStorage.
 *
 */
class EmptyStorage extends AbstractDataStorage
{
    /** @var string */
    protected $dataGroup;
    /** @var string */
    protected $idKey;

    /**
     * Populates an entity with storage data.
     *
     * @param DataEntityInterface $entity
     * @param array               $data
     * @return void
     */
    protected function populateEntity(DataEntityInterface &$entity, array $data) : void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $entity->{$method}($value);
        }
    }

    /**
     * Get data from an entity.
     *
     * @param DataEntityInterface $entity
     * @return array
     */
    protected function getEntityData(DataEntityInterface $entity) : array
    {
        /** @var EmptyEntity $entity */
        return $entity->storage;
    }

    /**
     * Sets id key fir the storage. Only for unit test.
     *
     * @param string $idKey
     *
     * @return EmptyStorage
     */
    public function setIdKey($idKey)
    {
        $this->idKey = $idKey;
        return $this;
    }

    /**
     * Sets data group for the storage. Only for unit test.
     *
     * @param string $dataGroup
     *
     * @return EmptyStorage
     */
    public function setDataGroup($dataGroup)
    {
        $this->dataGroup = $dataGroup;
        return $this;
    }
}
