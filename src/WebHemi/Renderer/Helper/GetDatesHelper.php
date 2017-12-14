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

namespace WebHemi\Renderer\Helper;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage;
use WebHemi\Data\StorageInterface;
use WebHemi\Data\Traits\StorageInjectorTrait;
use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;
use WebHemi\Router\ProxyInterface;

/**
 * Class GetDatesHelper
 *
 * @method Storage\ApplicationStorage getApplicationStorage()
 * @method Storage\Filesystem\FilesystemStorage getFilesystemStorage()
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 */
class GetDatesHelper implements HelperInterface
{
    /** @var EnvironmentInterface */
    private $environmentManager;

    use StorageInjectorTrait;

    /**
     * GetDatesHelper constructor.
     *
     * @param EnvironmentInterface $environmentManager
     * @param StorageInterface[] ...$dataStorages
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
        return 'getDates';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getDates(order_by, limit) }}';
    }

    /**
     * Gets helper options for the render.
     *
     * @return array
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
        return 'Returns the archive dates for the current application.';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        $dates = [];

        /** @var Storage\ApplicationStorage $applicationStorage */
        $applicationStorage = $this->getApplicationStorage();
        /** @var Storage\Filesystem\FilesystemStorage $filesystemStorage */
        $filesystemStorage = $this->getFilesystemStorage();
        /** @var Storage\Filesystem\FilesystemDirectoryStorage $directoryStorage */
        $directoryStorage = $this->getFilesystemDirectoryStorage();

        if (!$applicationStorage || !$filesystemStorage || !$directoryStorage) {
            return [];
        }

        /** @var Entity\ApplicationEntity $application */
        $application = $applicationStorage
            ->getApplicationByName($this->environmentManager->getSelectedApplication());
        $applicationId = $application->getKeyData();

        /** @var array $categoryDirectoryData */
        $categoryDirectoryData = $directoryStorage
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_ARCHIVE);

        // Basically we get publications here, but only one per month and that is what we need. The date...
        /** @var Entity\Filesystem\FilesystemEntity[] $contents */
        $contents = $filesystemStorage
            ->getPublishedDocuments(
                $applicationId,
                [],
                'date_published ASC',
                null,
                null,
                'YEAR(date_published), MONTH(date_published)'
            );

        /** @var Entity\Filesystem\FilesystemEntity $content */
        foreach ($contents as $content) {
            /** @var DateTime $date */
            $date = $content->getDatePublished();

            $dates[] = [
                'name' => $date->format('Y-m'),
                'path' => $categoryDirectoryData['uri'],
                'dateTime' => $date
            ];
        }

        return $dates;
    }
}
