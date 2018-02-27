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
 * Class FilesystemDocumentEntity
 */
class FilesystemDocumentEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem_document' => null,
        'fk_parent_revision' => null,
        'fk_author' => null,
        'content_revision' => null,
        'content_lead' => null,
        'content_body' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemDocumentEntity
     */
    public function setFilesystemDocumentId(int $identifier) : FilesystemDocumentEntity
    {
        $this->container['id_filesystem_document'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemDocumentId() : ? int
    {
        return !is_null($this->container['id_filesystem_document'])
            ? (int) $this->container['id_filesystem_document']
            : null;
    }

    /**
     * @param null|int $parentRevisionIdentifier
     * @return FilesystemDocumentEntity
     */
    public function setParentRevisionId(? int $parentRevisionIdentifier) : FilesystemDocumentEntity
    {
        $this->container['fk_parent_revision'] = $parentRevisionIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParentRevisionId() : ? int
    {
        return !is_null($this->container['fk_parent_revision'])
            ? (int) $this->container['fk_parent_revision']
            : null;
    }

    /**
     * @param null|int $authorIdentifier
     * @return FilesystemDocumentEntity
     */
    public function setAuthorId(? int $authorIdentifier) : FilesystemDocumentEntity
    {
        $this->container['fk_author'] = $authorIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAuthorId() : ? int
    {
        return !is_null($this->container['fk_author'])
            ? (int) $this->container['fk_author']
            : null;
    }

    /**
     * @param int $contentRevision
     * @return FilesystemDocumentEntity
     */
    public function setContentRevision(? int $contentRevision) : FilesystemDocumentEntity
    {
        $this->container['content_revision'] = $contentRevision;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getContentRevision() : ? int
    {
        return !is_null($this->container['content_revision'])
            ? (int) $this->container['content_revision']
            : null;
    }

    /**
     * @param string $contentLead
     * @return FilesystemDocumentEntity
     */
    public function setContentLead(string $contentLead) : FilesystemDocumentEntity
    {
        $this->container['content_lead'] = $contentLead;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentLead() : ? string
    {
        return $this->container['content_lead'];
    }

    /**
     * @param string $contentBody
     * @return FilesystemDocumentEntity
     */
    public function setContentBody(string $contentBody) : FilesystemDocumentEntity
    {
        $this->container['content_body'] = $contentBody;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentBody() : ? string
    {
        return $this->container['content_body'];
    }

    /**
     * @param DateTime $dateTime
     * @return FilesystemDocumentEntity
     */
    public function setDateCreated(DateTime $dateTime) : FilesystemDocumentEntity
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
     * @return FilesystemDocumentEntity
     */
    public function setDateModified(DateTime $dateTime) : FilesystemDocumentEntity
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
}
