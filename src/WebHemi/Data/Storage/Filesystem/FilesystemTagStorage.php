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

namespace WebHemi\Data\Storage\Filesystem;

use WebHemi\DateTime;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\Data\Entity\Filesystem\FilesystemTagEntity;

/**
 * Class FilesystemTagStorage.
 */
class FilesystemTagStorage extends AbstractStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_filesystem_tag';
    /** @var string */
    protected $idKey = 'id_filesystem_tag';
    /** @var string */
    private $idApplication = 'fk_application';
    /** @var string */
    private $name = 'name';
    /** @var string */
    private $title = 'title';
    /** @var string */
    private $description = 'description';
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
        /* @var FilesystemTagEntity $dataEntity */
        $dataEntity->setFilesystemTagId((int) $data[$this->idKey])
            ->setApplicationId((int) $data[$this->idApplication])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
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
        /** @var FilesystemTagEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => (int) $dataEntity->getKeyData(),
            $this->idApplication => (int) $dataEntity->getApplicationId(),
            $this->title => $dataEntity->getTitle(),
            $this->name => $dataEntity->getName(),
            $this->description => $dataEntity->getDescription(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Gets the filesystem tag entity by the identifier.
     *
     * @param int $identifier
     * @return null|FilesystemTagEntity
     */
    public function getFilesystemTagById(int $identifier) : ? FilesystemTagEntity
    {
        /** @var null|FilesystemTagEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Gets the filesystem tag entity list by the application identifier.
     *
     * @param int $applicationId
     * @return FilesystemTagEntity[]
     */
    public function getFilesystemTagsByApplication(int $applicationId) : array
    {
        return $this->getDataEntitySet([$this->idApplication => $applicationId]);
    }

    /**
     * @param int $filesystemId
     * @return FilesystemTagEntity[]
     */
    public function getFilesystemTagsByFilesystem(int $filesystemId) : array
    {
        $entitySet = [];

        /** @var ConnectorInterface $connector */
        $connector = $this->getConnector();

        // Switch to another data group (DO NOT FORGET TO SET IT BACK!!)
        $connector->setDataGroup('webhemi_filesystem_to_filesystem_tag')
            ->setIdKey('id_filesystem_to_filesystem_tag');

        // Direct query to avoid to populate non existing entity...
        $connectedTags = $connector->getDataSet(['fk_filesystem' => $filesystemId]);
        $tagIds = [];

        foreach ($connectedTags as $recordSet) {
            $tagIds[] = $recordSet['fk_filesystem_tag'];
        }

        // switch back to the original data group
        $connector->setDataGroup($this->dataGroup)
            ->setIdKey($this->idKey);

        if (!empty($tagIds)) {
            $entitySet = $this->getDataEntitySet([$this->idKey => $tagIds]);
        }

        return $entitySet;
    }
}
