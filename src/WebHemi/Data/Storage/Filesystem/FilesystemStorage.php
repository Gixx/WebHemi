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

use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Entity\Filesystem\FilesystemEntity;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\DateTime;

/**
 * Class FilesystemStorage.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class FilesystemStorage extends AbstractStorage
{
    public const TYPE_DOCUMENT = FilesystemEntity::TYPE_DOCUMENT;
    public const TYPE_BINARY = FilesystemEntity::TYPE_BINARY;
    public const TYPE_DIRECTORY = FilesystemEntity::TYPE_DIRECTORY;
    public const TYPE_SYMLINK = FilesystemEntity::TYPE_SYMLINK;

    /** @var string */
    protected $dataGroup = 'webhemi_filesystem';
    /** @var string */
    protected $idKey = 'id_filesystem';
    /** @var string */
    private $idApplication = 'fk_application';
    /** @var string */
    private $idCategory = 'fk_category';
    /** @var string */
    private $idParent = 'fk_parent_node';
    /** @var string */
    private $idDocument = 'fk_filesystem_document';
    /** @var string */
    private $idFile = 'fk_filesystem_file';
    /** @var string */
    private $idDirectory = 'fk_filesystem_directory';
    /** @var string */
    private $idLink = 'fk_filesystem_link';
    /** @var string */
    private $path = 'path';
    /** @var string */
    private $baseName = 'basename';
    /** @var string */
    private $title = 'title';
    /** @var string */
    private $description = 'description';
    /** @var string */
    private $isHidden = 'is_hidden';
    /** @var string */
    private $isReadOnly = 'is_read_only';
    /** @var string */
    private $isDeleted = 'is_deleted';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';
    /** @var string */
    private $datePublished = 'date_published';

    /**
     * Populates an entity with storage data.
     *
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity) - sorry, this will remain like this. It's a complex object, period.
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var FilesystemEntity $dataEntity */
        $dataEntity->setFilesystemId((int) $data[$this->idKey])
            ->setApplicationId((int) $data[$this->idApplication])
            ->setCategoryId(isset($data[$this->idCategory]) ? (int) $data[$this->idCategory] : null)
            ->setParentId(isset($data[$this->idParent]) ? (int) $data[$this->idParent] : null)
            ->setDocumentId(isset($data[$this->idDocument]) ? (int) $data[$this->idDocument] : null)
            ->setFileId(isset($data[$this->idFile]) ? (int) $data[$this->idFile] : null)
            ->setDirectoryId(isset($data[$this->idDirectory]) ? (int) $data[$this->idDirectory] : null)
            ->setLinkId(isset($data[$this->idLink]) ? (int) $data[$this->idLink] : null)
            ->setPath($data[$this->path])
            ->setBaseName($data[$this->baseName])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setHidden((bool) $data[$this->isHidden])
            ->setReadOnly((bool) $data[$this->isReadOnly])
            ->setDeleted((bool) $data[$this->isDeleted])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(!empty($data[$this->dateModified]) ? new DateTime($data[$this->dateModified]) : null)
            ->setDatePublished(!empty($data[$this->datePublished]) ? new DateTime($data[$this->datePublished]) : null);
    }

    /**
     * Get data from an entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /** @var FilesystemEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();
        $datePublished = $dataEntity->getDatePublished();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->idApplication = $dataEntity->getApplicationId(),
            $this->idCategory = $dataEntity->getCategoryId(),
            $this->idParent = $dataEntity->getParentId(),
            $this->documentId = $dataEntity->getDocumentId(),
            $this->fileId = $dataEntity->getFileId(),
            $this->directoryId = $dataEntity->getDirectoryId(),
            $this->linkId = $dataEntity->getLinkId(),
            $this->path => $dataEntity->getPath(),
            $this->baseName => $dataEntity->getBaseName(),
            $this->title => $dataEntity->getTitle(),
            $this->description => $dataEntity->getDescription(),
            $this->isHidden => (int) $dataEntity->getHidden(),
            $this->isReadOnly => (int) $dataEntity->getReadOnly(),
            $this->isDeleted => (int) $dataEntity->getDeleted(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null,
            $this->datePublished => $datePublished instanceof DateTime ? $datePublished->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Gets the filesystem entity by the identifier.
     *
     * @param int $identifier
     * @return null|FilesystemEntity
     */
    public function getFilesystemById(int $identifier) : ? FilesystemEntity
    {
        /** @var null|FilesystemEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Gets the filesystem entity set by application and directory.
     *
     * @param int $applicationId
     * @param int $directoryId
     * @return FilesystemEntity[]
     */
    public function getFilesystemSetByApplicationAndDirectory(int $applicationId, int $directoryId) : ? array
    {
        /** @var FilesystemEntity[] $dataEntitySet */
        $dataEntitySet = $this->getDataEntitySet(
            [$this->idApplication => $applicationId, $this->idDirectory => $directoryId]
        );

        return $dataEntitySet;
    }

    /**
     * Gets the filesystem entity set by application and tag.
     *
     * @param int $applicationId
     * @param int $tagId
     * @return FilesystemEntity[]
     */
    public function getFilesystemSetByApplicationAndTag(int $applicationId, int $tagId) : ? array
    {
        /** @var ConnectorInterface $connector */
        $connector = $this->getConnector();

        // Switch to another data group (DO NOT FORGET TO SET IT BACK!!)
        $connector->setDataGroup('webhemi_filesystem_to_filesystem_tag')
            ->setIdKey('id_filesystem_to_filesystem_tag');

        $dataSet = $connector->getDataSet(['fk_filesystem_tag' => $tagId]);
        $filesystemIds = [];

        foreach ($dataSet as $data) {
            $filesystemIds[] = $data['fk_filesystem'];
        }

        // switch back to the original data group
        $connector->setDataGroup($this->dataGroup)
            ->setIdKey($this->idKey);

        return empty($filesystemIds)
            ? []
            : $this->getPublishedDocuments(
                $applicationId,
                [$this->idKey => $filesystemIds]
            );
    }

    /**
     * Gets the filesystem entity by application and path.
     *
     * @param int $applicationId
     * @param string $path
     * @param string $baseName
     * @return null|FilesystemEntity
     */
    public function getFilesystemByApplicationAndPath(
        int $applicationId,
        string $path,
        string $baseName
    ) : ? FilesystemEntity {
        /** @var null|FilesystemEntity $dataEntity */
        $dataEntity = $this->getDataEntity(
            [
                $this->idApplication => $applicationId,
                $this->path => $path,
                $this->baseName => $baseName
            ]
        );

        return $dataEntity;
    }

    /**
     * Gets the published documents
     *
     * @param int $applicationId
     * @param array $additionalExpressions
     * @param string|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $groupBy
     * @param string|null $having
     * @return FilesystemEntity[]
     */
    public function getPublishedDocuments(
        int $applicationId,
        array $additionalExpressions = [],
        string $order = null,
        int $limit = null,
        int $offset = null,
        string $groupBy = null,
        string $having = null
    ) : array {
        $defaultExpressions = [
            $this->idApplication => $applicationId,
            $this->isHidden => 0,
            $this->isDeleted => 0,
            $this->datePublished => true, // >> IS NOT NULL
            $this->idDocument => true // >> will get documents only
        ];

        // This way the default ones can be overwritten.
        $expressions = array_merge($defaultExpressions, $additionalExpressions);

        $options = [
            ConnectorInterface::OPTION_ORDER => ($order ?? $this->datePublished.' DESC')
        ];

        if (is_numeric($limit)) {
            $options[ConnectorInterface::OPTION_LIMIT] = (int) $limit;

            if (is_numeric($offset)) {
                $options[ConnectorInterface::OPTION_OFFSET] = (int) $offset;
            }
        }

        if (!empty($groupBy)) {
            $options[ConnectorInterface::OPTION_GROUP] = $groupBy;

            if (!empty($having)) {
                $options[ConnectorInterface::OPTION_HAVING] = $having;
            }
        }

        /** @var FilesystemEntity[] $dataEntitySet */
        $dataEntitySet = $this->getDataEntitySet($expressions, $options);

        return $dataEntitySet;
    }

    /**
     * Gets simple structured meta information for a filesystem record.
     *
     * @param int $filesystemId
     * @return array
     */
    public function getPublicationMeta(int $filesystemId) : array
    {
        $filesystemMetaSet = [];

        /** @var ConnectorInterface $connector */
        $connector = $this->getConnector();

        // Switch to another data group (DO NOT FORGET TO SET IT BACK!!)
        $connector->setDataGroup('webhemi_filesystem_meta')
            ->setIdKey('id_filesystem_meta');

        $filesystemRecord = $connector->getDataSet(['fk_filesystem' => $filesystemId]);

        // switch back to the original data group
        $connector->setDataGroup($this->dataGroup)
            ->setIdKey($this->idKey);

        foreach ($filesystemRecord as $data) {
            $filesystemMetaSet[$data['meta_key']] = $data['meta_data'];
        }

        return $filesystemMetaSet;
    }
}
