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

namespace WebHemi\Middleware\Action;

use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\ActionMiddlewareInterface;

/**
 * Class AbstractMiddlewareAction.
 */
abstract class AbstractMiddlewareAction implements MiddlewareInterface, ActionMiddlewareInterface
{
    /** @var ServerRequestInterface */
    protected $request;
    /** @var ResponseInterface */
    protected $response;

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    abstract public function getTemplateName() : string;

    /**
     * Gets template data.
     *
     * @return array
     */
    abstract public function getTemplateData() : array;

    /**
     * Invokes the middleware action.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     */
    final public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        // Init
        $this->request = $request;
        $this->response = $response;

        // Process
        $templateData = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, []);
        $templateData = array_merge($templateData, $this->getTemplateData());

        // Save
        $request = $this->request
            ->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_TEMPLATE, $this->getTemplateName())
            ->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, $templateData);
        $response = $this->response;
    }
}
