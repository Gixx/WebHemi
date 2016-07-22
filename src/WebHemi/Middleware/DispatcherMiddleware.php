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

use Psr\Http\Message\StreamInterface;
use RuntimeException;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
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
     * From the request data renders an output for the response, or sets an error status code.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @throws RuntimeException
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        /** @var MiddlewareInterface $actionMiddleware */
        $actionMiddleware = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE);

        // If there is a valid action Middleware, then dispatch it.
        if (!is_null($actionMiddleware) && $actionMiddleware instanceof MiddlewareActionInterface) {
            $response = $actionMiddleware($request, $response);

            /** @var string $template */
            $template = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_TEMPLATE);
            /** @var array $data */
            $data = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);
            /** @var StreamInterface $body */
            $body = $this->templateRenderer->render($template, $data);
            $response = $response->withBody($body);
        } else {
            throw new RuntimeException(sprintf('The given attribute is not a valid Action Middleware.'));
        }

        return $response;
    }
}
