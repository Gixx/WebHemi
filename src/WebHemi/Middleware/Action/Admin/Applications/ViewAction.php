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
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class ViewAction
 */
class ViewAction extends AbstractMiddlewareAction
{
    /** @var ConfigInterface */
    private $configuration;
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var EnvironmentManager */
    private $environmentManager;

    /**
     * DashboardAction constructor.
     *
     * @param ConfigInterface $configuration
     * @param AuthAdapterInterface $authAdapter
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(
        ConfigInterface $configuration,
        AuthAdapterInterface $authAdapter,
        EnvironmentManager $environmentManager
    ) {
        $this->configuration = $configuration;
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
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
        return [];
    }
}
