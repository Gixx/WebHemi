<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity;

use WebHemi\DateTime;

/**
 * Class FilesystemEntity
 */
class FilesystemEntity extends AbstractEntity
{
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_BINARY = 'binary';
    public const TYPE_DIRECTORY = 'directory';
    public const TYPE_SYMLINK = 'symlink';

    /**
     * @var array
     */
    protected $container = [
        'id_filesystem' => null,
        'fk_application' => null,
        'fk_category' => null,
        'fk_parent_node' => null,
        'fk_filesystem_document' => null,
        'fk_filesystem_file' => null,
        'fk_filesystem_directory' => null,
        'fk_filesystem_link' => null,
        'path' => null,
        'basename' => null,
        'uri' => null,
        'title' => null,
        'description' => null,
        'is_hidden' => null,
        'is_read_only' => null,
        'is_deleted' => null,
        'date_created' => null,
        'date_modified' => null,
        'date_published' => null,
    ];

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        if (!empty($this->container['fk_filesystem_file'])) {
            return self::TYPE_BINARY;
        }
        if (!empty($this->container['fk_filesystem_directory'])) {
            return self::TYPE_DIRECTORY;
        }
        if (!empty($this->container['fk_filesystem_link'])) {
            return self::TYPE_SYMLINK;
        }
        return self::TYPE_DOCUMENT;
    }

    /**
     * @param int $identifier
     * @return FilesystemEntity
     */
    public function setFilesystemId(int $identifier) : FilesystemEntity
    {
        $this->container['id_filesystem'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemId() : ? int
    {
        return !is_null($this->container['id_filesystem'])
            ? (int) $this->container['id_filesystem']
            : null;
    }

    /**
     * @param int $applicationIdentifier
     * @return FilesystemEntity
     */
    public function setApplicationId(int $applicationIdentifier) : FilesystemEntity
    {
        $this->container['fk_application'] = $applicationIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationId() : ? int
    {
        return !is_null($this->container['fk_application'])
            ? (int) $this->container['fk_application']
            : null;
    }

    /**
     * @param int $categoryIdentifier
     * @return FilesystemEntity
     */
    public function setCategoryId(int $categoryIdentifier) : FilesystemEntity
    {
        $this->container['fk_category'] = $categoryIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCategoryId() : ? int
    {
        return !is_null($this->container['fk_category'])
            ? (int) $this->container['fk_category']
            : null;
    }

    /**
     * @param null|int $parentNodeIdentifier
     * @return FilesystemEntity
     */
    public function setParentNodeId(? int $parentNodeIdentifier) : FilesystemEntity
    {
        $this->container['fk_parent_node'] = $parentNodeIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParentNodeId() : ? int
    {
        return !is_null($this->container['fk_parent_node'])
            ? (int) $this->container['fk_parent_node']
            : null;
    }

    /**
     * @param null|int $documentIdentifier
     * @return FilesystemEntity
     */
    public function setFilesystemDocumentId(? int $documentIdentifier) : FilesystemEntity
    {
        $this->container['fk_filesystem_document'] = $documentIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemDocumentId() : ? int
    {
        return !is_null($this->container['fk_filesystem_document'])
            ? (int) $this->container['fk_filesystem_document']
            : null;
    }

    /**
     * @param null|int $fileIdentifier
     * @return FilesystemEntity
     */
    public function setFilesystemFileId(? int $fileIdentifier) : FilesystemEntity
    {
        $this->container['fk_filesystem_file'] = $fileIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemFileId() : ? int
    {
        return !is_null($this->container['fk_filesystem_file'])
            ? (int) $this->container['fk_filesystem_file']
            : null;
    }

    /**
     * @param null|int $directoryIdentifier
     * @return FilesystemEntity
     */
    public function setFilesystemDirectoryId(? int $directoryIdentifier) : FilesystemEntity
    {
        $this->container['fk_filesystem_directory'] = $directoryIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemDirectoryId() : ? int
    {
        return !is_null($this->container['fk_filesystem_directory'])
            ? (int) $this->container['fk_filesystem_directory']
            : null;
    }

    /**
     * @param null|int $linkIdentifier
     * @return FilesystemEntity
     */
    public function setFilesystemLinkId(? int $linkIdentifier) : FilesystemEntity
    {
        $this->container['fk_filesystem_link'] = $linkIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemLinkId() : ? int
    {
        return !is_null($this->container['fk_filesystem_link'])
            ? (int) $this->container['fk_filesystem_link']
            : null;
    }

    /**
     * @param string $path
     * @return FilesystemEntity
     */
    public function setPath(string $path) : FilesystemEntity
    {
        $this->container['path'] = $path;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath() : ? string
    {
        return $this->container['path'];
    }

    /**
     * @param string $baseName
     * @return FilesystemEntity
     */
    public function setBaseName(string $baseName) : FilesystemEntity
    {
        $this->container['basename'] = $baseName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBaseName() : ? string
    {
        return $this->container['basename'];
    }

    /**
     * @param string $uri
     * @return FilesystemEntity
     */
    public function setUri(string $uri) : FilesystemEntity
    {
        $this->container['uri'] = $uri;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUri() : ? string
    {
        return $this->container['uri'];
    }

    /**
     * @param string $title
     * @return FilesystemEntity
     */
    public function setTitle(string $title) : FilesystemEntity
    {
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->container['title'];
    }

    /**
     * @param string $description
     * @return FilesystemEntity
     */
    public function setDescription(string $description) : FilesystemEntity
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->container['description'];
    }

    /**
     * @param bool $isHidden
     * @return FilesystemEntity
     */
    public function setIsHidden(bool $isHidden) : FilesystemEntity
    {
        $this->container['is_hidden'] = $isHidden ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsHidden() : bool
    {
        return !empty($this->container['is_hidden']);
    }

    /**
     * @param bool $isReadonly
     * @return FilesystemEntity
     */
    public function setIsReadOnly(bool $isReadonly) : FilesystemEntity
    {
        $this->container['is_read_only'] = $isReadonly ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsReadOnly() : bool
    {
        return !empty($this->container['is_read_only']);
    }

    /**
     * @param bool $isDeleted
     * @return FilesystemEntity
     */
    public function setIsDeleted(bool $isDeleted) : FilesystemEntity
    {
        $this->container['is_deleted'] = $isDeleted ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDeleted() : bool
    {
        return !empty($this->container['is_deleted']);
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemEntity
    {
        $this->container['date_created'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return !empty($this->container['date_created'])
            ? new DateTime($this->container['date_created'])
            : null;
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemEntity
    {
        $this->container['date_modified'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return !empty($this->container['date_modified'])
            ? new DateTime($this->container['date_modified'])
            : null;
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemEntity
     */
    public function setDatePublished(DateTime $dateTime) : FilesystemEntity
    {
        $this->container['date_published'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDatePublished() : ? DateTime
    {
        return !empty($this->container['date_published'])
            ? new DateTime($this->container['date_published'])
            : null;
    }
}
