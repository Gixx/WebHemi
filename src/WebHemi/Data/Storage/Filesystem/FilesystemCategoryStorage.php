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

use WebHemi\DateTime;
use WebHemi\Data\EntityInterface;
use WebHemi\Data\Storage\AbstractStorage;
use WebHemi\Data\Entity\Filesystem\FilesystemCategoryEntity;

/**
 * Class FilesystemCategoryStorage.
 */
class FilesystemCategoryStorage extends AbstractStorage
{
    /**
     * @var string
     */
    protected $dataGroup = 'webhemi_filesystem_category';
    /**
     * @var string
     */
    protected $idKey = 'id_filesystem_category';
    /**
     * @var string
     */
    private $idApplication = 'fk_application';
    /**
     * @var string
     */
    private $name = 'name';
    /**
     * @var string
     */
    private $title = 'title';
    /**
     * @var string
     */
    private $description = 'description';
    /**
     * @var string
     */
    private $itemOrder = 'item_order';
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
         * @var FilesystemCategoryEntity $dataEntity
         */
        $dataEntity->setFilesystemCategoryId((int) $data[$this->idKey])
            ->setApplicationId((int) $data[$this->idApplication])
            ->setName($data[$this->name])
            ->setTitle($data[$this->title])
            ->setDescription($data[$this->description])
            ->setItemOrder($data[$this->itemOrder] ?? 'DESC')
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
         * @var FilesystemCategoryEntity $dataEntity
         */
        $dateCreated = $dataEntity->getDateCreated();
        $dateModified = $dataEntity->getDateModified();

        return [
            $this->idKey => (int) $dataEntity->getKeyData(),
            $this->idApplication => (int) $dataEntity->getApplicationId(),
            $this->title => $dataEntity->getTitle(),
            $this->name => $dataEntity->getName(),
            $this->description => $dataEntity->getDescription(),
            $this->itemOrder => $dataEntity->getItemOrder(),
            $this->dateCreated => $dateCreated instanceof DateTime ? $dateCreated->format('Y-m-d H:i:s') : null,
            $this->dateModified => $dateModified instanceof DateTime ? $dateModified->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Gets the filesystem category entity by the identifier.
     *
     * @param  int $identifier
     * @return null|FilesystemCategoryEntity
     */
    public function getFilesystemCategoryById(int $identifier) : ? FilesystemCategoryEntity
    {
        /**
         * @var null|FilesystemCategoryEntity $dataEntity
         */
        $dataEntity = $this->getDataEntity([$this->idKey => $identifier]);

        return $dataEntity;
    }

    /**
     * Gets the filesystem category entity by the application identifier and name.
     *
     * @param  int    $applicationId
     * @param  string $name
     * @return null|FilesystemCategoryEntity
     */
    public function getFilesystemCategoryByApplicationAndName(
        int $applicationId,
        string $name
    ) : ? FilesystemCategoryEntity {
        /**
         * @var null|FilesystemCategoryEntity $dataEntity
         */
        $dataEntity = $this->getDataEntity(
            [
                $this->idApplication => $applicationId,
                $this->name => $name
            ]
        );

        return $dataEntity;
    }

    /**
     * Gets the filesystem category entity list by the application identifier.
     *
     * @param  int $applicationId
     * @return FilesystemCategoryEntity[]
     */
    public function getFilesystemCategoriesByApplication(int $applicationId) : ? array
    {
        return $this->getDataEntitySet([$this->idApplication => $applicationId]);
    }
}
