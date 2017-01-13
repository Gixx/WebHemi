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
declare(strict_types=1);

namespace WebHemi\Middleware;

use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;

/**
 * Class AbstractMiddlewareAction.
 */
abstract class AbstractMiddlewareAction implements MiddlewareInterface, MiddlewareActionInterface
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
        $this->request = $request;
        $this->response = $response;

        $templateData = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, []);
        $templateData = array_merge($templateData, $this->getTemplateData());

        $request = $this->request
            ->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_TEMPLATE, $this->getTemplateName())
            ->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, $templateData);
        $response = $this->response;
    }
}
