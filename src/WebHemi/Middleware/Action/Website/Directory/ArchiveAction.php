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

namespace WebHemi\Middleware\Action\Website\Directory;

use RuntimeException;
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
        $date = $parameters['uri_parameter'] ?? null;

        if (!$date) {
            throw new RuntimeException('Forbidden', 403);
        }

        $dateParts = explode('-', $date);

        if (!preg_match('/^\d{4}\-\d{2}$/', $date) || !checkdate((int) ($dateParts[1] ?? 13), 1, (int) $dateParts[0])) {
            throw new RuntimeException('Bad Request', 400);
        }

        /** @var Entity\ApplicationEntity $applicationEntity */
        $applicationEntity = $this->getApplicationStorage()
            ->getApplicationByName($this->environmentManager->getSelectedApplication());

        /** @var Entity\Filesystem\FilesystemEntity[] $publications */
        $publications = $this->getFilesystemStorage()
            ->getPublishedDocuments(
                $applicationEntity->getApplicationId(),
                [
                    'YEAR(date_published) = ?' => (int) $dateParts[0],
                    'MONTH(date_published) = ?' => (int) $dateParts[1]
                ]
            );

        if (!$publications) {
            throw new RuntimeException('Not Found', 404);
        }

        /** @var DateTime $titleDate */
        $titleDate = $publications[0]->getDatePublished();

        /** @var Entity\Filesystem\FilesystemEntity $filesystemEntity */
        foreach ($publications as $filesystemEntity) {
            $blogPosts[] = $this->getBlobPostData($applicationEntity, $filesystemEntity);
        }

        return [
            'page' => [
                'title' => $titleDate->format('Y4B'),
                'type' => 'Archive',
            ],
            'activeMenu' => $date,
            'blogPosts' => $blogPosts,
        ];
    }
}
