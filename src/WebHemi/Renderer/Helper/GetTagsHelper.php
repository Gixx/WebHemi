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

namespace WebHemi\Renderer\Helper;

use WebHemi\Data\Entity;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\FilesystemStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;
use WebHemi\Router\ProxyInterface;

/**
 * Class GetTagsHelper
 */
class GetTagsHelper implements HelperInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;

    /**
     * @var ApplicationStorage
     */
    private $applicationStorage;

    /**
     * @var FilesystemStorage
     */
    private $filesystemStorage;

    /**
     * GetTagsHelper constructor.
     *
     * @param EnvironmentInterface $environmentManager
     * @param ApplicationStorage $applicationStorage
     * @param FilesystemStorage $filesystemStorage
     */
    public function __construct(
        EnvironmentInterface $environmentManager,
        ApplicationStorage $applicationStorage,
        FilesystemStorage $filesystemStorage
    ) {
        $this->environmentManager = $environmentManager;
        $this->applicationStorage = $applicationStorage;
        $this->filesystemStorage = $filesystemStorage;
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'getTags';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{{ getTags(order_by, limit) }}';
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
     * @codeCoverageIgnore - plain text
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

        /**
         * @var Entity\ApplicationEntity $application
         */
        $application = $this->applicationStorage
            ->getApplicationByName($this->environmentManager->getSelectedApplication());
        $applicationId = $application->getApplicationId();

        /**
         * @var Entity\FilesystemDirectoryDataEntity $categoryDirectoryData
         */
        $categoryDirectoryData = $this->filesystemStorage
            ->getFilesystemDirectoryDataByApplicationAndProxy((int) $applicationId, ProxyInterface::LIST_TAG);

        /**
         * @var Entity\EntitySet $tagList
         */
        $tagList = $this->filesystemStorage
            ->getFilesystemTagListByApplication((int) $applicationId);

        foreach ($tagList as $tagEntity) {
            $tags[] = [
                'path' => $categoryDirectoryData->getUri(),
                'name' => $tagEntity->getName(),
                'title' => $tagEntity->getTitle()
            ];
        }

        return $tags;
    }
}
