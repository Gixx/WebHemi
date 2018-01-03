<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
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

    /**
     * Returns the routing parameters.
     *
     * @return array
     */
    protected function getRoutingParameters() : array
    {
        return $this->request->getAttribute('routing_parameters');
    }

    /**
     * Returns the query paramters. This is the processed and filtered _GET data.
     *
     * @return array
     */
    protected function getGetParameters() : array
    {
        return $this->request->getQueryParams();
    }

    /**
     * Returns the post paramters. This is the processed and filtered _POST data.
     *
     * @return array
     */
    protected function getPostParameters() : array
    {
        return $this->request->getParsedBody();
    }

    /**
     * Returns data from the upladed files.
     *
     * @return array
     */
    protected function getUploadedFiles() : array
    {
        return $this->request->getUploadedFiles();
    }

    /**
     * Returns all kind of parameters.
     *
     * @return array
     */
    protected function getAllParameters() : array
    {
        return [
            'ROUTE' => $this->getRoutingParameters(),
            'GET'   => $this->getGetParameters(),
            'POST'  => $this->getPostParameters(),
            'FILES' => $this->getUploadedFiles()
        ];
    }
}
