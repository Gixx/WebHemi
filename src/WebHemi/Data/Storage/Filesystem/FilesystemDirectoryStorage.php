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

namespace WebHemi\Data\Storage\Filesystem;

use RuntimeException;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\Data\Entity\Filesystem\FilesystemDirectoryEntity;
use WebHemi\DateTime;

/**
 * Class FilesystemDirectoryStorage.
 */
class FilesystemDirectoryStorage extends AbstractStorage
{
    /**
     * @var string
     */
    protected $dataGroup = 'webhemi_filesystem_directory';
    /**
     * @var string
     */
    protected $idKey = 'id_filesystem_directory';
    /**
     * @var string
     */
    private $description = 'description';
    /**
     * @var string
     */
    private $directoryType = 'directory_type';
    /**
     * @var string
     */
    private $proxy = 'proxy';
    /**
     * @var string
     */
    private $isAutoIndex = 'is_autoindex';
    /**
     * @var string
     */
    private $dateCreated = 'date_created';
    /**
     * @var string
     */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param  EntityInterface $dataEntity
     * @param  array           $data
     * @return void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /**
         * @var FilesystemDirectoryEntity $dataEntity
         */
        $dataEntity->setFilesystemDirectoryId((int) $data[$this->idKey])
            ->setDescription($data[$this->description])
            ->setDirectoryType($data[$this->directoryType])
            ->setProxy($data[$this->proxy])
            ->setAutoIndex((bool) $data[$this->isAutoIndex])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(!empty($data[$this->dateModified]) ? new DateTime($data[$this->dateModified]) : null);
    }

    /**
     * Get data from an entity.
     *
     * @param  EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /**
         * @var FilesystemDirectoryEntity $dataEntity
         */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => $dataEntity->getKeyData(),
            $this->description => $dataEntity->getDescription(),
            $this->directoryType => $dataEntity->getDirectoryType(),
            $this->proxy => $dataEntity->getProxy(),
            $this->isAutoIndex => (int) $dataEntity->getAutoIndex(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Gets the filesystem directory entity by the identifier.
     *
     * @param  int $identifier
     * @return null|FilesystemDirectoryEntity
     */
    public function getFilesystemDirectoryById(int $identifier) : ? FilesystemDirectoryEntity
    {
        /**
         * @var null|FilesystemDirectoryEntity $dataEntity
         */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Gets the filesystem directory entity by the proxy.
     * From named proxies only one exists form each type.
     *
     * @param  string $proxy
     * @return null|FilesystemDirectoryEntity
     */
    public function getFilesystemDirectoryByProxy(string $proxy) : ? FilesystemDirectoryEntity
    {
        /**
         * @var null|FilesystemDirectoryEntity $dataEntity
         */
        $dataEntity = $this->getDataEntity([$this->proxy => $proxy]);

        return $dataEntity;
    }

    /**
     * Collects complex information about
     *
     * @param  int    $applicationId
     * @param  string $proxy
     * @throws RuntimeException
     * @return array
     */
    public function getDirectoryDataByApplicationAndProxy(int $applicationId, string $proxy) : array
    {
        /**
         * @var FilesystemDirectoryEntity $directoryEntity
         */
        $directoryEntity = $this->getFilesystemDirectoryByProxy($proxy);

        if (!$directoryEntity instanceof FilesystemDirectoryEntity) {
            throw new RuntimeException(sprintf('Unknown proxy given: %s', $proxy), 1000);
        }

        // MUST exist
        $filesystemData = $this->getFilesystemData($applicationId, $directoryEntity->getFilesystemDirectoryId());
        $uri = $filesystemData['path'].'/'.$filesystemData['basename'];

        if (strpos($uri, '//') !== false) {
            $uri = str_replace('//', '/', $uri);
        }

        return [
            'id_filesystem_direcory' => $directoryEntity->getFilesystemDirectoryId(),
            'description' => $directoryEntity->getDescription(),
            'type' => $directoryEntity->getDirectoryType(),
            'is_autoindex' => $directoryEntity->getAutoIndex(),
            'id_application' => $applicationId,
            'id_filesystem' => $filesystemData['id_filesystem'],
            'path' => $filesystemData['path'],
            'basename' => $filesystemData['basename'],
            'uri' => $uri,
            'title' => $filesystemData['title']
        ];
    }

    /**
     * @param int $applicationId
     * @param int $directoryId
     * @return array
     */
    private function getFilesystemData(int $applicationId, int $directoryId) : array
    {
        /**
         * @var ConnectorInterface $connector
         */
        $connector = $this->getConnector();

        // Switch to another data group (DO NOT FORGET TO SET IT BACK!!)
        $connector->setDataGroup('webhemi_filesystem')
            ->setIdKey('id_filesystem');

        $filesystemRecord = $connector->getDataSet(
            ['fk_application' => $applicationId, 'fk_filesystem_directory' => $directoryId],
            [ConnectorInterface::OPTION_LIMIT => 1]
        );

        // switch back to the original data group
        $connector->setDataGroup($this->dataGroup)
            ->setIdKey($this->idKey);

        return $filesystemRecord[0] ?? [];
    }
}
