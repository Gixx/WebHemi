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

use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Form\PresetInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;
    /**
     * @var AuthInterface
     */
    protected $authAdapter;
    /**
     * @var EnvironmentInterface
     */
    protected $environmentManager;
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
     * @param ConfigurationInterface $configuration
     * @param AuthInterface          $authAdapter
     * @param EnvironmentInterface   $environmentManager
     * @param ApplicationStorage     $applicationStorage
     * @param PresetInterface        $applicationFormPreset
     * @param CSRFInterface          $csrfAdapter
     */
    public function __construct(
        ConfigurationInterface $configuration,
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager,
        ApplicationStorage $applicationStorage,
        PresetInterface $applicationFormPreset,
        CSRFInterface $csrfAdapter
    ) {
        $this->configuration = $configuration;
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
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
        $applications = $this->applicationStorage->getApplicationList();
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
