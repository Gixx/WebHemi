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

namespace WebHemi\Middleware\Action\Admin\Applications;

use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Form\PresetInterface;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * @var ApplicationStorage
     */
    protected $applicationStorage;
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
     * @param PresetInterface        $applicationFormPreset
     * @param CSRFInterface          $csrfAdapter
     */
    public function __construct(
        ApplicationStorage $applicationStorage,
        PresetInterface $applicationFormPreset,
        CSRFInterface $csrfAdapter
    ) {
        $this->applicationStorage = $applicationStorage;
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
        return 'admin-applications-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        /** @var EntitySet $applications */
        $applications = $this->applicationStorage->getApplicationList();
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
