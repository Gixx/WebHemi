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

namespace WebHemi\Middleware\Action\Website\Directory;

use RuntimeException;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity;
use WebHemi\DateTime;
use WebHemi\Middleware\Action\Website\IndexAction;

/**
 * Class ArchiveAction.
 */
class ArchiveAction extends IndexAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-post-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $blogPosts = [];
        $parameters = $this->getRoutingParameters();
        $date = $parameters['basename'] ?? null;

        if ($parameters['path'] == '/' || empty($date)) {
            throw new RuntimeException('Forbidden', 403);
        }

        $dateParts = explode('-', $date);

        if (!preg_match('/^\d{4}\-\d{2}$/', $date) || !checkdate((int) ($dateParts[1] ?? 13), 1, (int) $dateParts[0])) {
            throw new RuntimeException('Bad Request', 400);
        }

        /**
         * @var Entity\ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /**
         * @var Entity\EntitySet $publications
         */
        $publications = $this->getFilesystemStorage()
            ->getFilesystemPublishedDocumentListByDate(
                (int) $applicationEntity->getApplicationId(),
                (int) $dateParts[0],
                (int) $dateParts[1]
            );

        if (empty($publications)) {
            throw new RuntimeException('Not Found', 404);
        }

        /**
         * @var DateTime $titleDate
         */
        $titleDate = $publications[0]->getDatePublished();

        /**
         * @var Entity\FilesystemPublishedDocumentEntity $publishedDocumentEntity
         */
        foreach ($publications as $publishedDocumentEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $publishedDocumentEntity);
        }

        return [
            'page' => [
                'title' => $titleDate->format('Y4B'),
                'type' => 'Archive',
            ],
            'activeMenu' => $date,
            'Application' => $applicationEntity,
            'blogPosts' => $blogPosts,
        ];
    }
}
