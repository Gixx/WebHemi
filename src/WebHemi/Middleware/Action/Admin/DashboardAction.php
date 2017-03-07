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

namespace WebHemi\Middleware\Action\Admin;

use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class DashboardAction.
 */
class DashboardAction extends AbstractMiddlewareAction
{
    /** @var AuthInterface */
    private $authAdapter;
    /** @var EnvironmentInterface */
    private $environmentManager;

    /**
     * DashboardAction constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager
    ) {
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
        return 'admin-dashboard';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        // @TODO TBD
        return [
            'user' => $this->authAdapter->getIdentity(),
        ];
    }
}
