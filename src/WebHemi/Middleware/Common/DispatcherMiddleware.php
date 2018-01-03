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

namespace WebHemi\Middleware\Common;

use Psr\Http\Message\StreamInterface;
use RuntimeException;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\ActionMiddlewareInterface;
use WebHemi\Renderer\ServiceInterface as RendererInterface;

/**
 * Class DispatcherMiddleware.
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /** @var RendererInterface */
    private $templateRenderer;

    /**
     * DispatcherMiddleware constructor.
     *
     * @param RendererInterface $templateRenderer
     */
    public function __construct(RendererInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * From the request data renders an output for the response, or sets an error status code.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @throws RuntimeException
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        /** @var MiddlewareInterface $actionMiddleware */
        $actionMiddleware = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE);

        // If there is a valid action Middleware, then dispatch it.
        if (!is_null($actionMiddleware) && $actionMiddleware instanceof ActionMiddlewareInterface) {
            /** @var ResponseInterface $response */
            $actionMiddleware($request, $response);

            // Create template only when there's no redirect
            if (ResponseInterface::STATUS_REDIRECT != $response->getStatusCode()) {
                /** @var string $template */
                $template = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_TEMPLATE);
                /** @var array $data */
                $data = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);

                if (!$request->isXmlHttpRequest()) {
                    /** @var StreamInterface $body */
                    $body = $this->templateRenderer->render($template, $data);
                    $response = $response->withBody($body);
                }
            }
        } else {
            throw new RuntimeException(sprintf('The given attribute is not a valid Action Middleware.'), 1000);
        }
    }
}
