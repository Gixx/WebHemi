<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Applications;

use Exception;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ResponseInterface;

/**
 * Class AddAction.
 */
class AddAction extends ListAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-control-panel-applications-add';
    }

    /**
     * Gets template data.
     *
     * @throws Exception
     * @return array
     */
    public function getTemplateData() : array
    {
        // TODO complete form
        /** @var array $postData */
        $postData = $this->request->getParsedBody();
        /** @var HtmlForm $form */
        $form = $this->applicationFormPreset->getPreset();
        $form->loadData($postData);

        if ($form->validate()) {
            // TODO validate name
            // * in case of domain: no other application with the same domain (== name)
            // * in case of directory: no other application with the same path (== name)
            //   + no filesystem path within the same path
        } else {
            throw new Exception('Form validation failed', ResponseInterface::STATUS_BAD_REQUEST);
        }

        $applicationUri = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_APPLICATION_URI);

        $this->response = $this->response
            ->withStatus(ResponseInterface::STATUS_REDIRECT, 'Found')
            ->withHeader('Location', $applicationUri.'applications');

        return [];
    }
}
