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
namespace WebHemi\Data\Storage;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\ApplicationEntity;

/**
 * Class ApplicationStorage.
 */
class ApplicationStorage extends AbstractDataStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_application';
    /** @var string */
    protected $idKey = 'id_application';
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
    protected function populateEntity(DataEntityInterface &$entity, array $data)
    {
        /* @var ApplicationEntity $entity */
        $entity->setApplicationId($data[$this->idKey])
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
        /** @var ApplicationEntity $entity */
        $dateCreated = $entity->getDateCreated();
        $dateModified = $entity->getDateModified();

        return [
            $this->idKey => $entity->getKeyData(),
            $this->name => $entity->getName(),
            $this->title => $entity->getTitle(),
            $this->description => $entity->getDescription(),
            $this->isReadOnly => (int)$entity->getReadOnly(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a Application entity identified by (unique) ID.
     *
     * @param int $identifier
     *
     * @return null|ApplicationEntity
     */
    public function getApplicationById($identifier)
    {
        $entity = null;
        $data = $this->getDataAdapter()->getData($identifier);

        if (!empty($data)) {
            /** @var ApplicationEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $data);
        }

        return $entity;
    }

    /**
     * Returns an Application entity by name.
     *
     * @param string $name
     *
     * @return null|ApplicationEntity
     */
    public function getApplicationByName($name)
    {
        $entity = null;
        $dataList = $this->getDataAdapter()->getDataSet([$this->name => $name], 1);

        if (!empty($dataList)) {
            /** @var ApplicationEntity $entity */
            $entity = $this->createEntity();
            $this->populateEntity($entity, $dataList[0]);
        }

        return $entity;
    }
}
