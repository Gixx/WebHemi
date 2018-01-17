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

namespace WebHemi\Middleware\Action\Auth;

use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class LogoutAction.
 */
class LogoutAction extends AbstractMiddlewareAction
{
    /**
     * @var AuthInterface
     */
    private $authAdapter;
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;

    /**
     * LogoutAction constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(AuthInterface $authAdapter, EnvironmentInterface $environmentManager)
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
