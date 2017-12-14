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

use WebHemi\DateTime;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\Data\Entity\Filesystem\FilesystemDocumentEntity;

/**
 * Class FilesystemDocumentStorage.
 */
class FilesystemDocumentStorage extends AbstractStorage
{
    /** @var string */
    protected $dataGroup = 'webhemi_filesystem_document';
    /** @var string */
    protected $idKey = 'id_filesystem_document';
    /** @var string */
    protected $idParentRevision = 'fk_parent_revision';
    /** @var string */
    protected $idAuthor = 'fk_author';
    /** @var string */
    protected $contentRevision = 'content_revision';
    /** @var string */
    protected $contentLead = 'content_lead';
    /** @var string */
    protected $contentBody = 'content_body';
    /** @var string */
    private $dateCreated = 'date_created';
    /** @var string */
    private $dateModified = 'date_modified';

    /**
     * Populates an entity with storage data.
     *
     * @param EntityInterface $dataEntity
     * @param array           $data
     * @return void
     */
    protected function populateEntity(EntityInterface&$dataEntity, array $data) : void
    {
        /* @var FilesystemDocumentEntity $dataEntity */
        $dataEntity->setFilesystemDocumentId((int) $data[$this->idKey])
            ->setParentRevisionId(isset($data[$this->idParentRevision]) ? (int) $data[$this->idParentRevision] : null)
            ->setAuthorId(isset($data[$this->idAuthor]) ? (int) $data[$this->idAuthor] : null)
            ->setContentRevision((int) ($data[$this->contentRevision] ?? 1))
            ->setContentLead($data[$this->contentLead])
            ->setContentBody($data[$this->contentBody])
            ->setDateCreated(new DateTime($data[$this->dateCreated] ?? 'now'))
            ->setDateModified(!empty($data[$this->dateModified]) ? new DateTime($data[$this->dateModified]) : null);
    }

    /**
     * Get data from an entity.
     *
     * @param EntityInterface $dataEntity
     * @return array
     */
    protected function getEntityData(EntityInterface $dataEntity) : array
    {
        /** @var FilesystemDocumentEntity $dataEntity */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => (int) $dataEntity->getKeyData(),
            $this->idParentRevision => $dataEntity->getParentRevisionId(),
            $this->idAuthor => $dataEntity->getAuthorId(),
            $this->contentRevision => (int) $dataEntity->getContentRevision(),
            $this->contentLead => $dataEntity->getContentLead(),
            $this->contentBody => $dataEntity->getContentBody(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Gets the filesystem document entity by the identifier.
     *
     * @param int $identifier
     * @return null|FilesystemDocumentEntity
     */
    public function getFilesystemDocumentById(int $identifier) : ? FilesystemDocumentEntity
    {
        /** @var null|FilesystemDocumentEntity $dataEntity */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Gets the published documents
     *
     * @param array $filesystemDocumentIds
     * @param array $additionalExpressions
     * @param string|null $order
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $groupBy
     * @param string|null $having
     * @return FilesystemDocumentEntity[]
     */
    public function getFilesystemDocuments(
        array $filesystemDocumentIds,
        array $additionalExpressions = [],
        string $order = null,
        int $limit = null,
        int $offset = null,
        string $groupBy = null,
        string $having = null
    ) : ? array {
        $defaultExpressions = [$this->idKey => $filesystemDocumentIds];

        // This way the default ones can be overwritten.
        $expressions = array_merge($defaultExpressions, $additionalExpressions);

        $options = [
            ConnectorInterface::OPTION_ORDER => ($order ?? $this->dateCreated.' ASC')
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

        /** @var FilesystemDocumentEntity[] $dataEntitySet */
        $dataEntitySet = $this->getDataEntitySet($expressions, $options);

        return $dataEntitySet;
    }
}
