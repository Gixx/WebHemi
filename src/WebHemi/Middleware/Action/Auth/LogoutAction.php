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

namespace WebHemi\Middleware\Action\Auth;

use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class LogoutAction
 */
class LogoutAction extends AbstractMiddlewareAction
{
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var EnvironmentManager */
    private $environmentManager;

    /**
     * MetaDataAction constructor.
     *
     * @param AuthAdapterInterface $authAdapter
     * @param EnvironmentManager   $environmentManager
     */
    public function __construct(AuthAdapterInterface $authAdapter, EnvironmentManager $environmentManager)
    {
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
        return '';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $this->authAdapter->clearIdentity();
        $this->response = $this->response->withStatus(ResponseInterface::STATUS_REDIRECT, 'Found')
            ->withHeader('Location', $this->environmentManager->getSelectedApplicationUri());

        return [];
    }
}
