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
    private $isEnabled = 'is_enabled';
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
        /* @var ApplicationEntity $entity */
        $entity->setApplicationId((int) $data[$this->idKey])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setReadOnly((bool) $data[$this->isReadOnly])
            ->setEnabled((bool) $data[$this->isEnabled])
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
        /** @var ApplicationEntity $entity */
        $dateCreated = $entity->getDateCreated();
        $dateModified = $entity->getDateModified();

        return [
            $this->idKey => $entity->getKeyData(),
            $this->name => $entity->getName(),
            $this->title => $entity->getTitle(),
            $this->description => $entity->getDescription(),
            $this->isReadOnly => (int) $entity->getReadOnly(),
            $this->isEnabled => (int) $entity->getEnabled(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Returns a Application entity identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|ApplicationEntity
     */
    public function getApplicationById($identifier) : ? ApplicationEntity
    {
        /** @var null|ApplicationEntity $entity */
        $entity = $this->getDataEntity([$this->idKey => $identifier]);

        return $entity;
    }

    /**
     * Returns an Application entity by name.
     *
     * @param string $name
     * @return null|ApplicationEntity
     */
    public function getApplicationByName($name) : ? ApplicationEntity
    {
        /** @var null|ApplicationEntity $entity */
        $entity = $this->getDataEntity([$this->name => $name]);

        return $entity;
    }
}
