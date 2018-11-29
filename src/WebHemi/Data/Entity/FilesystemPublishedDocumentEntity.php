<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity;

use WebHemi\DateTime;

/**
 * Class FilesystemPublishedDocumentEntity
 */
class FilesystemPublishedDocumentEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem' => null,
        'id_filesystem_document' => null,
        'fk_application' => null,
        'fk_category' => null,
        'fk_author' => null,
        'path' => null,
        'basename' => null,
        'uri' => null,
        'title' => null,
        'description' => null,
        'content_lead' => null,
        'content_body' => null,
        'date_published' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemPublishedDocumentEntity
     */
    public function setFilesystemId(int $identifier) : FilesystemPublishedDocumentEntity
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
     * @param int $identifier
     * @return FilesystemPublishedDocumentEntity
     */
    public function setFilesystemDocumentId(int $identifier) : FilesystemPublishedDocumentEntity
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
     * @param int $applicationIdentifier
     * @return FilesystemPublishedDocumentEntity
     */
    public function setApplicationId(int $applicationIdentifier) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setCategoryId(int $categoryIdentifier) : FilesystemPublishedDocumentEntity
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
     * @param int $authorIdentifier
     * @return FilesystemPublishedDocumentEntity
     */
    public function setAuthorId(int $authorIdentifier) : FilesystemPublishedDocumentEntity
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
     * @param string $path
     * @return FilesystemPublishedDocumentEntity
     */
    public function setPath(string $path) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setBaseName(string $baseName) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setUri(string $uri) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setTitle(string $title) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setDescription(string $description) : FilesystemPublishedDocumentEntity
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
     * @param string $contentLead
     * @return FilesystemPublishedDocumentEntity
     */
    public function setContentLead(string $contentLead) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setContentBody(string $contentBody) : FilesystemPublishedDocumentEntity
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
     * @return FilesystemPublishedDocumentEntity
     */
    public function setDatePublished(DateTime $dateTime) : FilesystemPublishedDocumentEntity
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
