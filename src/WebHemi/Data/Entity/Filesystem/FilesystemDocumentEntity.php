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
 * Class FilesystemDocumentEntity.
 */
class FilesystemDocumentEntity implements EntityInterface
{
    /** @var int */
    private $filesystemDocumentId;
    /** @var int */
    private $parentRevisionId;
    /** @var int */
    private $authorId;
    /** @var int */
    private $contentRevision;
    /** @var string */
    private $contentLead;
    /** @var string */
    private $contentBody;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return FilesystemDocumentEntity
     */
    public function setKeyData(int $entityId) : FilesystemDocumentEntity
    {
        $this->filesystemDocumentId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->filesystemDocumentId;
    }

    /**
     * @param int $filesystemDocumentId
     * @return FilesystemDocumentEntity
     */
    public function setFilesystemDocumentId(int $filesystemDocumentId) : FilesystemDocumentEntity
    {
        $this->filesystemDocumentId = $filesystemDocumentId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFilesystemDocumentId() : ? int
    {
        return $this->filesystemDocumentId;
    }

    /**
     * @param null|int $parentRevisionId
     * @return FilesystemDocumentEntity
     */
    public function setParentRevisionId(? int $parentRevisionId) : FilesystemDocumentEntity
    {
        $this->parentRevisionId = $parentRevisionId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getParentRevisionId() : ? int
    {
        return $this->parentRevisionId;
    }

    /**
     * @param null|int $authorId
     * @return FilesystemDocumentEntity
     */
    public function setAuthorId(? int $authorId) : FilesystemDocumentEntity
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAuthorId() : ? int
    {
        return $this->authorId;
    }

    /**
     * @param int $contentRevision
     * @return FilesystemDocumentEntity
     */
    public function setContentRevision(int $contentRevision) : FilesystemDocumentEntity
    {
        $this->contentRevision = $contentRevision;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getContentRevision() : ? int
    {
        return $this->contentRevision;
    }

    /**
     * @param string $contentLead
     * @return FilesystemDocumentEntity
     */
    public function setContentLead(string $contentLead) : FilesystemDocumentEntity
    {
        $this->contentLead = $contentLead;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentLead() : ? string
    {
        return $this->contentLead;
    }

    /**
     * @param string $contentBody
     * @return FilesystemDocumentEntity
     */
    public function setContentBody(string $contentBody) : FilesystemDocumentEntity
    {
        $this->contentBody = $contentBody;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentBody() : ? string
    {
        return $this->contentBody;
    }

    /**
     * @param DateTime $dateCreated
     * @return FilesystemDocumentEntity
     */
    public function setDateCreated(DateTime $dateCreated) : FilesystemDocumentEntity
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
     * @return FilesystemDocumentEntity
     */
    public function setDateModified(DateTime $dateModified) : FilesystemDocumentEntity
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
}
