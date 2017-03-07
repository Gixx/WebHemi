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
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Storage\AbstractStorage;

/**
 * Class ResourceStorage.
 */
class ResourceStorage extends AbstractStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_am_resource';
    /** @var string */
    protected $idKey = 'id_am_resource';
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
        /* @var ResourceEntity $dataEntity */
        $dataEntity->setResourceId((int) $data[$this->idKey])
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
        /** @var ResourceEntity $dataEntity */
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
     * Returns a Resource entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|ResourceEntity
     */
    public function getResourceById($identifier) : ? ResourceEntity
    {
        /** @var null|ResourceEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Returns an Resource entity by name.
     *
     * @param string $name
     * @return null|ResourceEntity
     */
    public function getResourceByName($name) : ? ResourceEntity
    {
        /** @var null|ResourceEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->name => $name]);

        return $dataEntity;
    }
}
