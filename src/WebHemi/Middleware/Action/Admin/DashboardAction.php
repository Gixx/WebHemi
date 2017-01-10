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

namespace WebHemi\Middleware\Action\Admin;

use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class DashboardAction
 */
class DashboardAction extends AbstractMiddlewareAction
{
    /** @var AuthAdapterInterface */
    private $authAdapter;

    /**
     * DashboardAction constructor.
     *
     * @param AuthAdapterInterface $authAdapter
     */
    public function __construct(AuthAdapterInterface $authAdapter)
    {
        $this->authAdapter = $authAdapter;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return 'admin-dashboard';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData()
    {
        // @TODO TBD
        return [
            'user' => $this->authAdapter->getIdentity()
        ];
    }
}
