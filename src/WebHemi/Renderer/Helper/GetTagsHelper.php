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
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;
use WebHemi\Router\ProxyInterface;

/**
 * Class GetTagsHelper
 *
 * @method Storage\ApplicationStorage getApplicationStorage()
 * @method Storage\Filesystem\FilesystemDirectoryStorage getFilesystemDirectoryStorage()
 * @method Storage\Filesystem\FilesystemTagStorage getFilesystemTagStorage()
 */
class GetTagsHelper implements HelperInterface
{
    /** @var EnvironmentInterface */
    private $environmentManager;

    use StorageInjectorTrait;

    /**
     * GetTagsHelper constructor.
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
        return 'getTags';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getTags(order_by, limit) }}';
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
        return 'Returns the tags for the current application.';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        $tags = [];

        /** @var Storage\ApplicationStorage $applicationStorage */
        $applicationStorage = $this->getApplicationStorage();
        /** @var Storage\Filesystem\FilesystemTagStorage $tagStorage */
        $tagStorage = $this->getFilesystemTagStorage();
        /** @var Storage\Filesystem\FilesystemDirectoryStorage $directoryStorage */
        $directoryStorage = $this->getFilesystemDirectoryStorage();

        if (!$applicationStorage || !$tagStorage || !$directoryStorage) {
            return [];
        }

        /** @var Entity\ApplicationEntity $application */
        $application = $applicationStorage->getApplicationByName($this->environmentManager->getSelectedApplication());
        $applicationId = $application->getKeyData();

        /** @var array $categoryDirectoryData */
        $categoryDirectoryData = $directoryStorage
            ->getDirectoryDataByApplicationAndProxy($applicationId, ProxyInterface::LIST_TAG);

        /** @var Entity\Filesystem\FilesystemTagEntity[] $tagList */
        $tagList = $tagStorage->getFilesystemTagsByApplication($applicationId);

        foreach ($tagList as $tagEntity) {
            $tags[] = [
                'path' => $categoryDirectoryData['uri'],
                'name' => $tagEntity->getName(),
                'title' => $tagEntity->getTitle()
            ];
        }

        return $tags;
    }
}
