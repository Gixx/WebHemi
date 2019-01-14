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

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Applications;

use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\DomainEntity;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\DomainStorage;
use WebHemi\Form\PresetInterface;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class ListAction.
 */
class ListAction extends AbstractMiddlewareAction
{
    /**
     * @var ApplicationStorage
     */
    protected $applicationStorage;
    /**
     * @var DomainStorage
     */
    protected $domainStorage;
    /**
     * @var PresetInterface
     */
    protected $applicationFormPreset;
    /**
     * @var CSRFInterface
     */
    protected $csrfAdapter;

    /**
     * IndexAction constructor.
     *
     * @param ApplicationStorage     $applicationStorage
     * @param DomainStorage          $domainStorage
     * @param PresetInterface        $applicationFormPreset
     * @param CSRFInterface          $csrfAdapter
     */
    public function __construct(
        ApplicationStorage $applicationStorage,
        DomainStorage $domainStorage,
        PresetInterface $applicationFormPreset,
        CSRFInterface $csrfAdapter
    ) {
        $this->applicationStorage = $applicationStorage;
        $this->domainStorage = $domainStorage;
        $this->applicationFormPreset = $applicationFormPreset;
        $this->csrfAdapter = $csrfAdapter;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-control-panel-applications-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        /** @var DomainEntity[] $domains */
        $domains = [];

        /** @var EntitySet $applications */
        $applications = $this->applicationStorage->getApplicationList();

        /** @var ApplicationEntity $applicationEntity */
        foreach ($applications as $key => $applicationEntity) {
            $domainId = $applicationEntity->getDomainId();

            if (!isset($domains[$domainId])) {
                $domains[$domainId] = $this->domainStorage->getDomainById($domainId);
            }

            if ($domains[$domainId] instanceof DomainEntity) {
                $applicationEntity->setReference(ApplicationEntity::REFERENCE_DOMAIN, $domains[$domainId]);
                $applications->offsetSet($key, $applicationEntity);
            }
        }

        /** @var HtmlForm $form */
        $form = $this->applicationFormPreset->getPreset();

        $csrfToken = $this->csrfAdapter->generate(180);

        $csrfElement = $form->getElement(CSRFInterface::SESSION_KEY);
        $csrfElement->setValues([$csrfToken]);

        return [
            'data' => $applications,
            'form' => $form,
            CSRFInterface::SESSION_KEY => $csrfToken
        ];
    }
}
