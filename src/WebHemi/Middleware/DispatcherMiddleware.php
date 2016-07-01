<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;

/**
 * Class DispatcherMiddleware.
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /** @var RendererAdapterInterface */
    private $templateRenderer;

    /**
     * DispatcherMiddleware constructor.
     *
     * @param RendererAdapterInterface $templateRenderer
     */
    public function __construct(RendererAdapterInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * From the request data renders an output for the response, or sets an error status code
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        $template = $request->getAttribute('template');
        $data = $request->getAttribute('data');
        /** @var StreamInterface $body */
        $body = $this->templateRenderer->render($template, $data);
        $response = $response->withBody($body);

        return $response;
    }
}
