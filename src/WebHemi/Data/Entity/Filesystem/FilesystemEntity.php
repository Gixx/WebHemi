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

namespace WebHemi\Data\Entity\Filesystem;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class FilesystemEntity.
 */
class FilesystemEntity implements EntityInterface
{
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_BINARY = 'binary';
    public const TYPE_DIRECTORY = 'directory';
    public const TYPE_SYMLINK = 'symlink';

    /** @var int */
    private $filesystemId;
    /** @var int */
    private $applicationId;
    /** @var int */
    private $categoryId;
    /** @var int */
    private $parentId;
    /** @var int */
    private $documentId;
    /** @var int */
    private $fileId;
    /** @var int */
    private $directoryId;
    /** @var int */
    private $linkId;
    /** @var string */
    private $path;
    /** @var string */
    private $baseName;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var bool */
    private $isHidden;
    /** @var bool */
    private $isReadOnly;
    /** @var bool */
    private $isDeleted;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $datePublished;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return FilesystemEntity
     */
    public function setKeyData(int $entityId) : FilesystemEntity
    {
        $this->filesystemId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->filesystemId;
    }

    /**
     * @param int $filesystemId
     * @return FilesystemEntity
     */
    public function setFilesystemId(int $filesystemId) : FilesystemEntity
    {
        $this->filesystemId = $filesystemId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFilesystemId() : ? int
    {
        return $this->filesystemId;
    }

    /**
     * @param int $applicationId
     * @return FilesystemEntity
     */
    public function setApplicationId(int $applicationId) : FilesystemEntity
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getApplicationId() : ? int
    {
        return $this->applicationId;
    }

    /**
     * @param null|int $categoryId
     * @return FilesystemEntity
     */
    public function setCategoryId(? int $categoryId) : FilesystemEntity
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCategoryId() : ? int
    {
        return $this->categoryId;
    }

    /**
     * @param int|null $parentId
     * @return FilesystemEntity
     */
    public function setParentId(? int $parentId) : FilesystemEntity
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getParentId() : ? int
    {
        return $this->parentId;
    }

    /**
     * @return FilesystemEntity
     */
    private function resetType() : FilesystemEntity
    {
        $this->documentId = $this->fileId = $this->directoryId = $this->linkId = null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        if (!empty($this->fileId)) {
            return self::TYPE_BINARY;
        }

        if (!empty($this->directoryId)) {
            return self::TYPE_DIRECTORY;
        }

        if (!empty($this->linkId)) {
            return self::TYPE_SYMLINK;
        }

        return self::TYPE_DOCUMENT;
    }

    /**
     * @param int|null $documentId
     * @return FilesystemEntity
     */
    public function setDocumentId(? int $documentId) : FilesystemEntity
    {
        if (!empty($documentId)) {
            $this->resetType();
        }

        $this->documentId = $documentId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDocumentId() : ? int
    {
        return $this->documentId;
    }

    /**
     * @param int|null $fileId
     * @return FilesystemEntity
     */
    public function setFileId(? int $fileId) : FilesystemEntity
    {
        if (!empty($fileId)) {
            $this->resetType();
        }

        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFileId() : ? int
    {
        return $this->fileId;
    }

    /**
     * @param int|null $directoryId
     * @return FilesystemEntity
     */
    public function setDirectoryId(? int $directoryId) : FilesystemEntity
    {
        if (!empty($directoryId)) {
            $this->resetType();
        }

        $this->directoryId = $directoryId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDirectoryId() : ? int
    {
        return $this->directoryId;
    }

    /**
     * @param int|null $linkId
     * @return FilesystemEntity
     */
    public function setLinkId(? int $linkId) : FilesystemEntity
    {
        if (!empty($linkId)) {
            $this->resetType();
        }

        $this->linkId = $linkId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLinkId() : ? int
    {
        return $this->linkId;
    }

    /**
     * @param string $path
     * @return FilesystemEntity
     */
    public function setPath(string $path) : FilesystemEntity
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @param string $baseName
     * @return FilesystemEntity
     */
    public function setBaseName(string $baseName) : FilesystemEntity
    {
        $this->baseName = $baseName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBaseName() : ? string
    {
        return $this->baseName;
    }

    /**
     * @param string $title
     * @return FilesystemEntity
     */
    public function setTitle(string $title) : FilesystemEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->title;
    }

    /**
     * @param string $description
     * @return FilesystemEntity
     */
    public function setDescription(string $description) : FilesystemEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->description;
    }

    /**
     * @param bool $state
     * @return FilesystemEntity
     */
    public function setHidden(bool $state) : FilesystemEntity
    {
        $this->isHidden = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHidden() : bool
    {
        return $this->isHidden ?? false;
    }

    /**
     * @param bool $state
     * @return FilesystemEntity
     */
    public function setReadOnly(bool $state) : FilesystemEntity
    {
        $this->isReadOnly = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly() : bool
    {
        return $this->isReadOnly ?? false;
    }

    /**
     * @param bool $state
     * @return FilesystemEntity
     */
    public function setDeleted(bool $state) : FilesystemEntity
    {
        $this->isDeleted = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeleted() : bool
    {
        return $this->isDeleted ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return FilesystemEntity
     */
    public function setDateCreated(DateTime $dateCreated) : FilesystemEntity
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateModified
     * @return FilesystemEntity
     */
    public function setDateModified(? DateTime $dateModified) : FilesystemEntity
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param DateTime $datePublished
     * @return FilesystemEntity
     */
    public function setDatePublished(? DateTime $datePublished) : FilesystemEntity
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDatePublished() : ? DateTime
    {
        return $this->datePublished;
    }
}
