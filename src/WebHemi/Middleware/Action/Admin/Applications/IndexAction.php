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

namespace WebHemi\Middleware\Action\Admin\Applications;

use WebHemi\Application\EnvironmentManager;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class IndexAction
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var ConfigInterface */
    private $configuration;
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var ApplicationStorage */
    private $applicationStorage;

    /**
     * DashboardAction constructor.
     *
     * @param ConfigInterface $configuration
     * @param AuthAdapterInterface $authAdapter
     * @param EnvironmentManager $environmentManager
     * @param ApplicationStorage $applicationStorage
     */
    public function __construct(
        ConfigInterface $configuration,
        AuthAdapterInterface $authAdapter,
        EnvironmentManager $environmentManager,
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
        return 'admin-applications-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $applications = $this->applicationStorage->getDataAdapter()->getDataSet([]);

        return [
            'applications' => $applications,
        ];
    }
}
