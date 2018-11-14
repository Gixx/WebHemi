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

use InvalidArgumentException;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Form\PresetInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class ViewAction.
 */
class ViewAction extends AbstractMiddlewareAction
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var AuthInterface
     */
    private $authAdapter;
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;
    /**
     * @var ApplicationStorage
     */
    private $applicationStorage;
    /**
     * @var PresetInterface
     */
    private $applicationFormPreset;
    /**
     * @var CSRFInterface
     */
    private $csrfAdapter;

    /**
     * ViewAction constructor.
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
        return 'admin-applications-view';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $params = $this->getRoutingParameters();

        $applicationName = $params['name'] ?? '';
        $applicationEntity = $this->applicationStorage->getApplicationByName($applicationName);

        if (!$applicationEntity instanceof ApplicationEntity) {
            throw new InvalidArgumentException(
                sprintf('%s is not a valid application name', $applicationName),
                404
            );
        }

        return [
            'data' => $applicationEntity,
            'csrf' => $this->csrfAdapter->generate()
        ];
    }
}
