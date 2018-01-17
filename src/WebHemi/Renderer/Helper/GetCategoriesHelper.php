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

namespace WebHemi\Renderer\Helper;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Data\StorageInterface;
use WebHemi\Data\Traits\StorageInjectorTrait;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;
use WebHemi\Router\ProxyInterface;

/**
 * Class GetCategoriesHelper
 *
 * @method Storage\ApplicationStorage getApplicationStorage()
 * @method Storage\Filesystem\FilesystemCategoryStorage getFilesystemCategoryStorage()
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 */
class GetCategoriesHelper implements HelperInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;

    use StorageInjectorTrait;

    /**
     * GetCategoriesHelper constructor.
     *
     * @param EnvironmentInterface $environmentManager
     * @param StorageInterface[]   ...$dataStorages
     */
    public function __construct(EnvironmentInterface $environmentManager, StorageInterface ...$dataStorages)
    {
        $this->environmentManager = $environmentManager;
        $this->addStorageInstances($dataStorages);
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'getCategories';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getCategories(order_by, limit) }}';
    }

    /**
     * Gets helper options for the render.
     *
     * @return             array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return 'Returns the categories for the current application.';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        $categories = [];

        /**
         * @var Storage\ApplicationStorage $applicationStorage
         */
        $applicationStorage = $this->getApplicationStorage();
        /**
         * @var Storage\Filesystem\FilesystemCategoryStorage $categoryStorage
         */
        $categoryStorage = $this->getFilesystemCategoryStorage();
        /**
         * @var Storage\Filesystem\FilesystemDirectoryStorage $directoryStorage
         */
        $directoryStorage = $this->getFilesystemDirectoryStorage();

        if (!$applicationStorage || !$categoryStorage || !$directoryStorage) {
            return [];
        }

        /**
         * @var Entity\ApplicationEntity $application
         */
        $application = $applicationStorage
            ->getApplicationByName($this->environmentManager->getSelectedApplication());
        $applicationId = $application->getKeyData();

        /**
         * @var array $categoryDirectoryData
         */
        $categoryDirectoryData = $directoryStorage
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_CATEGORY);

        /**
         * @var Entity\Filesystem\FilesystemCategoryEntity[] $categoryList
         */
        $categoryList = $categoryStorage
            ->getFilesystemCategoriesByApplication($applicationId);

        foreach ($categoryList as $categoryEntity) {
            $categories[] = [
                'path' => $categoryDirectoryData['uri'],
                'name' => $categoryEntity->getName(),
                'title' => $categoryEntity->getTitle()
            ];
        }

        return $categories;
    }
}
