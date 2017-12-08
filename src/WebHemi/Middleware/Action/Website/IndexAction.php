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

namespace WebHemi\Middleware\Action\Website;

use WebHemi\Data\StorageInterface;
use WebHemi\Data\Entity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;
use WebHemi\Middleware\Action\Traits;
use WebHemi\Data\Traits\StorageInjectorTrait;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var EnvironmentInterface */
    protected $environmentManager;

    use StorageInjectorTrait;
    use Traits\GetPublicationAuthorTrait;
    use Traits\GetPublicationPathTrait;
    use Traits\GetPublicationTagsTrait;
    use Traits\GetPublicationCategoryTrait;

    /**
     * IndexAction constructor.
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
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $blogPosts = [];

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getPublishedDocuments($applicationEntity->getApplicationId());

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
        }

        return [
            'activeMenu' => '',
            'blogPosts' => $blogPosts,
            'fixPost' => $applicationEntity->getIntroduction(),
        ];
    }

    /**
     * Collets the blog post data
     *
     * @param Entity\ApplicationEntity $applicationEntity
     * @param Entity\Filesystem\FilesystemEntity $filesystemEntity
     * @return array
     */
    protected function getBlobPostData(
        Entity\ApplicationEntity $applicationEntity,
        Entity\Filesystem\FilesystemEntity $filesystemEntity
    ) : array {
        /** @var Entity\Filesystem\FilesystemDocumentEntity $documentEntity */
        $documentEntity = $this->getFilesystemDocumentStorage()
            ->getFilesystemDocumentById($filesystemEntity->getDocumentId());

        $documentMeta = $this->getFilesystemStorage()
            ->getPublicationMeta($filesystemEntity->getFilesystemId());

        $author = $this->getPublicationAuthor(
            $documentEntity->getAuthorId(),
            $applicationEntity->getApplicationId()
        );
        $author['mood'] = [];

        if (isset($documentMeta['mood_key']) && isset($documentMeta['mood_name'])) {
            $author['mood'] = [
                $documentMeta['mood_name'],
                $documentMeta['mood_key']
            ];
        }

        return [
            'author' => $author,
            'tags' => $this->getPublicationTags(
                $applicationEntity->getApplicationId(),
                $filesystemEntity->getFilesystemId()
            ),
            'category' => $this->getPublicationCategory(
                $applicationEntity->getApplicationId(),
                $filesystemEntity->getCategoryId()
            ),
            'publishedAt' => $filesystemEntity->getDatePublished(),
            'location' => $documentMeta['location'] ?? '',
            'summary' => $filesystemEntity->getDescription(),
            'illustration' => $documentMeta['illustration'] ?? '',
            'path' => $this->getPublicationPath($filesystemEntity),
            'title' => $filesystemEntity->getTitle(),
            'contentLead' => $documentEntity->getContentLead(),
            'contentBody' => $documentEntity->getContentBody()
        ];
    }
}
