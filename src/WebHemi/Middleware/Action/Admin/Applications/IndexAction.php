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
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
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
     * IndexAction constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param AuthInterface          $authAdapter
     * @param EnvironmentInterface   $environmentManager
     * @param ApplicationStorage     $applicationStorage
     */
    public function __construct(
        ConfigurationInterface $configuration,
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager,
        ApplicationStorage $applicationStorage
    ) {
        $this->configuration = $configuration;
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
        $this->applicationStorage = $applicationStorage;
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

        return [
            'applications' => $applications,
        ];
    }
}
