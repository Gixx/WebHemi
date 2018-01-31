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

use WebHemi\Data\Query\QueryInterface;

/**
 * Class FilesystemStorage.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class FilesystemStorage extends AbstractStorage
{
    /**
     * Returns filesystem information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getFilesystemById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemById',
            [':idFilesystem' => $identifier]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns filesystem information identified by (unique) ID.
     *
     * @param  int $identifier
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemMetaListByFilesystem(
        int $identifier,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $list = [];
        $this->normalizeLimitAndOffset($limit, $offset);

        $metaDataList = $this->queryAdapter->fetchData(
            'getFilesystemMetaListByFilesystem',
            [
                ':idFilesystem' => $identifier,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($metaDataList as $metaData) {
            $list[$metaData['meta_key']] = $metaData['meta_data'];
        }

        return $list;
    }

    /**
     * Returns a set of filesystem data accroding to the application and directory ID.
     *
     * @param int $applicationId
     * @param int $directoryId
     * @param int $limit
     * @param int $offset
     * @return array|null
     */
    public function getFilesystemListByApplicationAndDirectory(
        int $applicationId,
        int $directoryId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $filesystemList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getFilesystemListByApplicationAndDirectory',
            [
                ':idApplication' => $applicationId,
                ':idDirectory' => $directoryId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $filesystemList[trim($row['path'], '/').'/'.$row['basename']] = $row;
        }

        return $filesystemList;
    }

    /**
     * Returns filesystem information by application, basename and path.
     *
     * @param  int    $applicationId
     * @param  string $path
     * @param  string $baseName
     * @return null|array
     */
    public function getFilesystemByApplicationAndPath(int $applicationId, string $path, string $baseName) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemByApplicationAndPath',
            [
                ':idApplication' => $applicationId,
                ':path' => $path,
                ':baseName' => $baseName
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns filesystem document information identified by (unique) ID.
     *
     * @param  int $identifier
     * @return null|array
     */
    public function getFilesystemDocumentById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemDocumentById',
            [':idDocument' => $identifier]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns the full filesystem document data set.
     *
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemDocumentList(
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $documentList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getFilesystemDocumentList',
            [
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $documentList[$row['id_filesystem_document']] = $row;
        }

        return $documentList;
    }

    /**
     * Returns published filesystem data along with the document data.
     *
     * @param int $applicationId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemPublishedDocumentList(
        int $applicationId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $filesystemList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->queryAdapter->fetchData(
            'getFilesystemPublishedDocumentList',
            [
                ':idApplication' => $applicationId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $filesystemList[trim($row['path'], '/').'/'.$row['basename']] = $row;
        }

        return $filesystemList;
    }

    /**
     * Returns published filesystem data according to a user ID along with the document data.
     *
     * @param int $applicationId
     * @param int $userId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemPublishedDocumentsByAuthor(
        int $applicationId,
        int $userId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $filesystemList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->queryAdapter->fetchData(
            'getFilesystemPublishedDocumentListByAuthor',
            [
                ':idApplication' => $applicationId,
                ':idUser' => $userId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $filesystemList[trim($row['path'], '/').'/'.$row['basename']] = $row;
        }

        return $filesystemList;
    }

    /**
     * Returns published filesystem data according to a tag ID along with the document data.
     *
     * @param int $applicationId
     * @param int $tagId
     * @param string|null $order
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemPublishedDocumentListByTag(
        int $applicationId,
        int $tagId,
        string $order = null,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $filesystemList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        if (empty($order)) {
            $order = 'fs.`date_published` DESC';
        }

        $data = $this->queryAdapter->fetchData(
            'getFilesystemPublishedDocumentListByTag',
            [
                ':idApplication' => $applicationId,
                ':idTag' => $tagId,
                ':orderBy' => $order,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $filesystemList[trim($row['path'], '/').'/'.$row['basename']] = $row;
        }

        return $filesystemList;
    }

    /**
     * Returns filesystem tag information identified by (unique) ID.
     *
     * @param int $identifier
     * @return array|null
     */
    public function getFilesystemTagById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemTagById',
            [
                ':idTag' => $identifier
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns the tags for a filesystem record.
     *
     * @param int $filesystemId
     * @param int $limit
     * @param int $offset
     * @return null|array
     */
    public function getFilesystemTagListByFilesystem(
        int $filesystemId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $tagList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getFilesystemTagListByFilesystem',
            [
                ':idFilesystem' => $filesystemId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $tagList[$row['name']] = $row;
        }

        return $tagList;
    }

    /**
     * Returns the tags for an application.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return array|null
     */
    public function getFilesystemTagListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    ) : ? array {
        $tagList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getFilesystemTagListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $tagList[$row['name']] = $row;
        }

        return $tagList;
    }

    /**
     * Returns filesystem category information identified by (unique) ID.
     *
     * @param int $identifier
     * @return array|null
     */
    public function getFilesystemCategoryById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemCategoryById',
            [
                ':idCategory' => $identifier
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns filesystem category information identified by application ID and category name.
     *
     * @param int $applicationId
     * @param string $categoryName
     * @return array|null
     */
    public function getFilesystemCategoryByApplicationAndName(int $applicationId, string $categoryName) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemCategoryByApplicationAndName',
            [
                ':idApplication' => $applicationId,
                ':categoryName' => $categoryName
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns the categories for an application.
     *
     * @param int $applicationId
     * @param int $limit
     * @param int $offset
     * @return array|null
     */
    public function getFilesystemCategoryListByApplication(
        int $applicationId,
        int $limit = QueryInterface::MAX_ROW_LIMIT,
        int $offset = 0
    )  : ? array {
        $categoryList = null;
        $this->normalizeLimitAndOffset($limit, $offset);

        $data = $this->queryAdapter->fetchData(
            'getFilesystemCategoryListByApplication',
            [
                ':idApplication' => $applicationId,
                ':limit' => $limit,
                ':offset' => $offset
            ]
        );

        foreach ($data as $row) {
            $categoryList[$row['name']] = $row;
        }

        return $categoryList;
    }

    /**
     * Returns filesystem directory information identified by (unique) ID.
     *
     * @param int $identifier
     * @return array|null
     */
    public function getFilesystemDirectoryById(int $identifier) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemDirectoryById',
            [
                ':idDirectory' => $identifier
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns filesystem directory information identified by it's proxy.
     *
     * @param string $proxy
     * @return array|null
     */
    public function getFilesystemDirectoryByProxy(string $proxy) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemDirectoryByProxy',
            [
                ':proxy' => $proxy
            ]
        );

        return $data[0] ?? null;
    }

    /**
     * Returns a combined information of the filesystem and directory according to the application and the proxy.
     *
     * @param int $applicationId
     * @param string $proxy
     * @return array|null
     */
    public function getFilesystemDataByApplicationAndProxy(int $applicationId, string $proxy) : ? array
    {
        $data = $this->queryAdapter->fetchData(
            'getFilesystemDataByApplicationAndProxy',
            [
                ':idApplication' => $applicationId,
                ':proxy' => $proxy
            ]
        );

        return $data[0] ?? null;
    }
}
