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

namespace WebHemi\Data\Storage;

use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\FilesystemCategoryEntity;
use WebHemi\Data\Entity\FilesystemDirectoryDataEntity;
use WebHemi\Data\Entity\FilesystemDirectoryEntity;
use WebHemi\Data\Entity\FilesystemDocumentEntity;
use WebHemi\Data\Entity\FilesystemEntity;
use WebHemi\Data\Entity\FilesystemMetaEntity;
use WebHemi\Data\Entity\FilesystemPublishedDocumentEntity;
use WebHemi\Data\Entity\FilesystemTagEntity;
use WebHemi\Data\Query\QueryInterface;

/**
 * Class FilesystemStorage.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class FilesystemStorage extends AbstractStorage
{
    /**
     * Returns a set of filesystem data accroding to the application and directory ID.
     *
     * @param int $applicationId
     * @param int $directoryId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemListByApplicationAndDirectory(
        int $applicationId,
        int $directoryId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemListByApplicationAndDirectory',
            [
                ':idApplication' => $applicationId,
                ':idDirectory' => $directoryId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemEntity::class, $data);
    }

    /**
     * Returns filesystem information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|FilesystemEntity
     */
    public function getFilesystemById(int $identifier) : ? FilesystemEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemById',
            [
                ':idFilesystem' => $identifier
            ]
        );

        /** @var null|FilesystemEntity $entity */
        $entity = $this->getEntity(FilesystemEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem information by application, basename and path.
     *
     * @param  int    $applicationId
     * @param  string $path
     * @param  string $baseName
     * @return null|FilesystemEntity
     */
    public function getFilesystemByApplicationAndPath(
        int $applicationId,
        string $path,
        string $baseName
    ) : ? FilesystemEntity {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemByApplicationAndPath',
            [
                ':idApplication' => $applicationId,
                ':path' => $path,
                ':baseName' => $baseName
            ]
        );

        /** @var null|FilesystemEntity $entity */
        $entity = $this->getEntity(FilesystemEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem meta list identified by filesystem ID.
     *
     * @param  int $identifier
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemMetaListByFilesystem(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemMetaListByFilesystem',
            [
                ':idFilesystem' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemMetaEntity::class, $data);
    }

    /**
     * Returns a simplified form of the filesystem meta list.
     *
     * @param  int $identifier
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getSimpleFilesystemMetaListByFilesystem(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $metaInfo = [];
        $entitySet = $this->getFilesystemMetaListByFilesystem($identifier, $limit, $offset);

        /** @var FilesystemMetaEntity $filesystemMetaEntity */
        foreach ($entitySet as $filesystemMetaEntity) {
            $metaInfo[$filesystemMetaEntity->getMetaKey()] = $filesystemMetaEntity->getMetaData();
        }

        return $metaInfo;
    }

    /**
     * Returns the full filesystem document data set.
     *
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemDocumentList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemDocumentList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemDocumentEntity::class, $data);
    }

    /**
     * Returns filesystem document information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|FilesystemDocumentEntity
     */
    public function getFilesystemDocumentById(int $identifier) : ? FilesystemDocumentEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemDocumentById',
            [
                ':idDocument' => $identifier
            ]
        );

        /** @var null|FilesystemDocumentEntity $entity */
        $entity = $this->getEntity(FilesystemDocumentEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns published filesystem data along with the document data.
     *
     * @param int $applicationId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemPublishedDocumentList(
        int $applicationId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentList',
            [
                ':idApplication' => $applicationId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemPublishedDocumentEntity::class, $data);
    }

    /**
     * Returns published filesystem data along with the document data.
     *
     * @param int $applicationId
     * @param int $year
     * @param int $month
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemPublishedDocumentListByDate(
        int $applicationId,
        int $year,
        int $month,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentListByDate',
            [
                ':idApplication' => $applicationId,
                ':year' => $year,
                ':month' => $month,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemPublishedDocumentEntity::class, $data);
    }

    /**
     * Returns published filesystem data along with the document data.
     *
     * @param int $applicationId
     * @param int $categoryId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemPublishedDocumentListByCategory(
        int $applicationId,
        int $categoryId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentListByCategory',
            [
                ':idApplication' => $applicationId,
                ':idCategory' => $categoryId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemPublishedDocumentEntity::class, $data);
    }

    /**
     * Returns published filesystem data according to a user ID along with the document data.
     *
     * @param int $applicationId
     * @param int $userId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemPublishedDocumentListByAuthor(
        int $applicationId,
        int $userId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentListByAuthor',
            [
                ':idApplication' => $applicationId,
                ':idUser' => $userId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemPublishedDocumentEntity::class, $data);
    }

    /**
     * Returns published filesystem data according to a tag ID along with the document data.
     *
     * @param int $applicationId
     * @param int $tagId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemPublishedDocumentListByTag(
        int $applicationId,
        int $tagId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentListByTag',
            [
                ':idApplication' => $applicationId,
                ':idTag' => $tagId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemPublishedDocumentEntity::class, $data);
    }

    /**
     * Returns filesystem information by application, basename and path.
     *
     * @param  int    $applicationId
     * @param  string $path
     * @param  string $baseName
     * @return null|FilesystemPublishedDocumentEntity
     */
    public function getFilesystemPublishedDocumentByApplicationAndPath(
        int $applicationId,
        string $path,
        string $baseName
    ) : ? FilesystemPublishedDocumentEntity {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentByApplicationAndPath',
            [
                ':idApplication' => $applicationId,
                ':path' => $path,
                ':baseName' => $baseName
            ]
        );

        /** @var null|FilesystemPublishedDocumentEntity $entity */
        $entity = $this->getEntity(FilesystemPublishedDocumentEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns a simplified list of publication dates.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getFilesystemPublishedDocumentDateList(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : array {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemPublishedDocumentDateList',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $data ?? [];
    }

    /**
     * Returns the tags for a filesystem record.
     *
     * @param int $filesystemId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemTagListByFilesystem(
        int $filesystemId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemTagListByFilesystem',
            [
                ':idFilesystem' => $filesystemId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemTagEntity::class, $data);
    }

    /**
     * Returns the tags for an application.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemTagListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemTagListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemTagEntity::class, $data);
    }

    /**
     * Returns filesystem tag information identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|FilesystemTagEntity
     */
    public function getFilesystemTagById(int $identifier) : ? FilesystemTagEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemTagById',
            [
                ':idTag' => $identifier
            ]
        );

        /** @var null|FilesystemTagEntity $entity */
        $entity = $this->getEntity(FilesystemTagEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem tag information identified by (unique) ID.
     *
     * @param int $applicationId
     * @param string $name
     * @return null|FilesystemTagEntity
     */
    public function getFilesystemTagByApplicationAndName(
        int $applicationId,
        string $name
    ) : ? FilesystemTagEntity {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemTagByApplicationAndName',
            [
                ':idApplication' => $applicationId,
                ':name' => $name
            ]
        );

        /** @var null|FilesystemTagEntity $entity */
        $entity = $this->getEntity(FilesystemTagEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns the categories for an application.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return EntitySet
     */
    public function getFilesystemCategoryListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    )  : EntitySet {
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemCategoryListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        return $this->getEntitySet(FilesystemCategoryEntity::class, $data);
    }

    /**
     * Returns filesystem category information identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|FilesystemCategoryEntity
     */
    public function getFilesystemCategoryById(int $identifier) : ? FilesystemCategoryEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemCategoryById',
            [
                ':idCategory' => $identifier
            ]
        );

        /** @var null|FilesystemCategoryEntity $entity */
        $entity = $this->getEntity(FilesystemCategoryEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem category information identified by application ID and category name.
     *
     * @param int $applicationId
     * @param string $categoryName
     * @return null|FilesystemCategoryEntity
     */
    public function getFilesystemCategoryByApplicationAndName(
        int $applicationId,
        string $categoryName
    ) : ? FilesystemCategoryEntity {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemCategoryByApplicationAndName',
            [
                ':idApplication' => $applicationId,
                ':categoryName' => $categoryName
            ]
        );

        /** @var null|FilesystemCategoryEntity $entity */
        $entity = $this->getEntity(FilesystemCategoryEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem directory information identified by (unique) ID.
     *
     * @param int $identifier
     * @return null|FilesystemDirectoryEntity
     */
    public function getFilesystemDirectoryById(int $identifier) : ? FilesystemDirectoryEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemDirectoryById',
            [
                ':idDirectory' => $identifier
            ]
        );

        /** @var null|FilesystemDirectoryEntity $entity */
        $entity = $this->getEntity(FilesystemDirectoryEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns filesystem directory information identified by its proxy.
     *
     * @param string $proxy
     * @return null|FilesystemDirectoryEntity
     */
    public function getFilesystemDirectoryByProxy(string $proxy) : ? FilesystemDirectoryEntity
    {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemDirectoryByProxy',
            [
                ':proxy' => $proxy
            ]
        );

        /** @var null|FilesystemDirectoryEntity $entity */
        $entity = $this->getEntity(FilesystemDirectoryEntity::class, $data[0] ?? []);

        return $entity;
    }

    /**
     * Returns a combined information of the filesystem and directory according to the application and the proxy.
     *
     * @param int $applicationId
     * @param string $proxy
     * @return null|FilesystemDirectoryDataEntity
     */
    public function getFilesystemDirectoryDataByApplicationAndProxy(
        int $applicationId,
        string $proxy
    ) : ? FilesystemDirectoryDataEntity {
        $data = $this->getQueryAdapter()->fetchData(
            'getFilesystemDirectoryDataByApplicationAndProxy',
            [
                ':idApplication' => $applicationId,
                ':proxy' => $proxy
            ]
        );

        /** @var null|FilesystemDirectoryDataEntity $entity */
        $entity = $this->getEntity(FilesystemDirectoryDataEntity::class, $data[0] ?? []);

        return $entity;
    }
}
