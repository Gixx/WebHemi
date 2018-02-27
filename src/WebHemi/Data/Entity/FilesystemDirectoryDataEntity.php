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
 * Class FilesystemDirectoryDataEntity
 */
class FilesystemDirectoryDataEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_filesystem' => null,
        'id_application' => null,
        'id_filesystem_directory' => null,
        'description' => null,
        'directory_type' => null,
        'proxy' => null,
        'is_autoindex' => null,
        'path' => null,
        'basename' => null,
        'uri' => null,
        'type' => null,
        'title' => null,
    ];

    /**
     * @param int $identifier
     * @return FilesystemDirectoryDataEntity
     */
    public function setFilesystemId(int $identifier) : FilesystemDirectoryDataEntity
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
     * @return FilesystemDirectoryDataEntity
     */
    public function setApplicationId(int $applicationIdentifier) : FilesystemDirectoryDataEntity
    {
        $this->container['id_application'] = $applicationIdentifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationId() : ? int
    {
        return !is_null($this->container['id_application'])
            ? (int) $this->container['id_application']
            : null;
    }

    /**
     * @param int $identifier
     * @return FilesystemDirectoryDataEntity
     */
    public function setFilesystemDirectoryId(int $identifier) : FilesystemDirectoryDataEntity
    {
        $this->container['id_filesystem_directory'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilesystemDirectoryId() : ? int
    {
        return !is_null($this->container['id_filesystem_directory'])
            ? (int) $this->container['id_filesystem_directory']
            : null;
    }

    /**
     * @param string $description
     * @return FilesystemDirectoryDataEntity
     */
    public function setDescription(string $description) : FilesystemDirectoryDataEntity
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
     * @param string $directoryType
     * @return FilesystemDirectoryDataEntity
     */
    public function setDirectoryType(string $directoryType) : FilesystemDirectoryDataEntity
    {
        $this->container['directory_type'] = $directoryType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDirectoryType() : ? string
    {
        return $this->container['directory_type'];
    }

    /**
     * @param string $proxy
     * @return FilesystemDirectoryDataEntity
     */
    public function setProxy(string $proxy) : FilesystemDirectoryDataEntity
    {
        $this->container['proxy'] = $proxy;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProxy() : ? string
    {
        return $this->container['proxy'];
    }

    /**
     * @param bool $isAutoindex
     * @return FilesystemDirectoryDataEntity
     */
    public function setIsAutoIndex(bool $isAutoindex) : FilesystemDirectoryDataEntity
    {
        $this->container['is_autoindex'] = $isAutoindex ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAutoIndex() : bool
    {
        return !empty($this->container['is_autoindex']);
    }

    /**
     * @param string $path
     * @return FilesystemDirectoryDataEntity
     */
    public function setPath(string $path) : FilesystemDirectoryDataEntity
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
     * @return FilesystemDirectoryDataEntity
     */
    public function setBaseName(string $baseName) : FilesystemDirectoryDataEntity
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
     * @return FilesystemDirectoryDataEntity
     */
    public function setUri(string $uri) : FilesystemDirectoryDataEntity
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
     * @param string $type
     * @return FilesystemDirectoryDataEntity
     */
    public function setType(string $type) : FilesystemDirectoryDataEntity
    {
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        return $this->container['type'];
    }

    /**
     * @param string $title
     * @return FilesystemDirectoryDataEntity
     */
    public function setTitle(string $title) : FilesystemDirectoryDataEntity
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
}
