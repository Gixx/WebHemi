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

namespace WebHemi\Application\ServiceAdapter\Base;

use Throwable;
use RuntimeException;
use Psr\Http\Message\StreamInterface;
use WebHemi\Application\ServiceInterface;
use WebHemi\Application\ServiceAdapter\AbstractAdapter;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\MiddlewarePipeline\ServiceInterface as PipelineInterface;
use WebHemi\Middleware\Common as CommonMiddleware;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Renderer\ServiceInterface as RendererInterface;
use WebHemi\Session\ServiceInterface as SessionInterface;

/**
 * Class ServiceAdapter
 */
class ServiceAdapter extends AbstractAdapter
{
    /**
     * Starts the session.
     *
     * @return ServiceInterface
     *
     * @codeCoverageIgnore - not testing session (yet)
     */
    public function initSession() : ServiceInterface
    {
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            return $this;
        }

        /**
         * @var SessionInterface $sessionManager
         */
        $sessionManager = $this->container->get(SessionInterface::class);
        /**
         * @var EnvironmentInterface $environmentManager
         */
        $environmentManager = $this->container->get(EnvironmentInterface::class);

        $name = $environmentManager->getSelectedApplication();
        $timeOut = 3600;
        $path = $environmentManager->getSelectedApplicationUri();
        $domain = $environmentManager->getApplicationDomain();
        $secure = $environmentManager->isSecuredApplication();
        $httpOnly = true;

        $sessionManager->start($name, $timeOut, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    /**
     * Runs the application. This is where the magic happens.
     * According tho the environment settings this must build up the middleware pipeline and execute it.
     *
     * a Pre-Routing Middleware can be; priority < 0:
     *  - LockCheck - check if the client IP is banned > S102|S403
     *  - Auth - if the user is not logged in, but there's a "Remember me" cookie, then logs in > S102
     *
     * Routing Middleware is fixed (RoutingMiddleware::class); priority = 0:
     *  - A middleware that routes the incoming Request and delegates to the matched middleware. > S102|S404|S405
     *    The RouteResult should be attached to the Request.
     *    If the Routing is not defined explicitly in the pipeline, then it will be injected with priority 0.
     *
     * a Post-Routing Middleware can be; priority between 0 and 100:
     *  - Acl - checks if the given route is available for the client. Also checks the auth > S102|S401|S403
     *  - CacheReader - checks if a suitable response body is cached. > S102|S200
     *
     * Dispatcher Middleware is fixed (DispatcherMiddleware::class); priority = 100:
     *  - A middleware which gets the corresponding Action middleware and applies it > S102
     *    If the Dispatcher is not defined explicitly in the pipeline, then it will be injected with priority 100.
     *    The Dispatcher should not set the response Status Code to 200 to let Post-Dispatchers to be called.
     *
     * a Post-Dispatch Middleware can be; priority > 100:
     *  - CacheWriter - writes response body into DataStorage (DB, File etc.) > S102
     *
     * Final Middleware is fixed (FinalMiddleware:class):
     *  - This middleware behaves a bit differently. It cannot be ordered, it's always the last called middleware:
     *    - when the middleware pipeline reached its end (typically when the Status Code is still 102)
     *    - when one item of the middleware pipeline returns with return response (status code is set to 200|40*|500)
     *    - when during the pipeline process an Exception is thrown.
     *
     * When the middleware pipeline is finished the application prints the header and the output.
     *
     * If a middleware other than the Routing, Dispatcher and Final Middleware has no priority set, it will be
     * considered to have priority = 50.
     *
     * @return ServiceInterface
     */
    public function run() : ServiceInterface
    {
        /**
         * @var PipelineInterface $pipelineManager
         */
        $pipelineManager = $this->container->get(PipelineInterface::class);

        try {
            /**
             * @var string $middlewareClass
             */
            $middlewareClass = $pipelineManager->start();

            while (!empty($middlewareClass)
                && is_string($middlewareClass)
                && $this->response->getStatusCode() == ResponseInterface::STATUS_PROCESSING
            ) {
                $this->invokeMiddleware($middlewareClass);
                $middlewareClass = $pipelineManager->next();
            };

            // If there was no error, we mark as ready for output.
            if ($this->response->getStatusCode() == ResponseInterface::STATUS_PROCESSING) {
                $this->response = $this->response->withStatus(ResponseInterface::STATUS_OK);
            }
        } catch (Throwable $exception) {
            $code = ResponseInterface::STATUS_INTERNAL_SERVER_ERROR;

            if (in_array(
                $exception->getCode(),
                [
                    ResponseInterface::STATUS_BAD_REQUEST,
                    ResponseInterface::STATUS_UNAUTHORIZED,
                    ResponseInterface::STATUS_FORBIDDEN,
                    ResponseInterface::STATUS_NOT_FOUND,
                    ResponseInterface::STATUS_BAD_METHOD,
                    ResponseInterface::STATUS_NOT_IMPLEMENTED,
                ]
            )
            ) {
                $code = $exception->getCode();
            }

            $this->response = $this->response->withStatus($code);
            $this->request = $this->request
                ->withAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION, $exception);
        }

        /**
         * @var CommonMiddleware\FinalMiddleware $finalMiddleware
         */
        $finalMiddleware = $this->container->get(CommonMiddleware\FinalMiddleware::class);
        // Check response and log errors if necessary
        $finalMiddleware($this->request, $this->response);

        return $this;
    }

    /**
     * Renders the response body and sends it to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    public function renderOutput() : void
    {
        // Create template only when there's no redirect
        if (!$this->request->isXmlHttpRequest()
            && ResponseInterface::STATUS_REDIRECT != $this->response->getStatusCode()
        ) {
            /**
             * @var RendererInterface $templateRenderer
             */
            $templateRenderer = $this->container->get(RendererInterface::class);
            /**
             * @var string $template
             */
            $template = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_TEMPLATE);
            /**
             * @var array $data
             */
            $data = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);
            /**
             * @var null|Throwable $exception
             */
            $exception = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION);

            // If there was any error, change the remplate
            if (!empty($exception)) {
                $template = 'error-'.$this->response->getStatusCode();
                $data['exception'] = $exception;
            }

            /**
             * @var StreamInterface $body
             */
            $body = $templateRenderer->render($template, $data);
            $this->response = $this->response->withBody($body);
        }

        $this->sendOutput();
    }

    /**
     * Sends the response body to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    public function sendOutput() : void
    {
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE') && headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent', 1000);
        }
        // @codeCoverageIgnoreEnd

        $output = $this->response->getBody();
        $contentLength = $this->response->getBody()->getSize();

        if ($this->request->isXmlHttpRequest()) {
            /**
             * @var array $templateData
             */
            $templateData = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);
            $templateData['output'] = (string) $output;
            /**
             * @var null|Throwable $exception
             */
            $exception = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION);

            if (!empty($exception)) {
                $templateData['exception'] = $exception;
            }

            $output = json_encode($templateData);
            $contentLength = strlen($output);
            $this->response = $this->response->withHeader('Content-Type', 'application/json; charset=UTF-8');
        }

        $this->injectContentLength($contentLength);
        $this->sendHttpHeader();
        $this->sendOutputHeaders($this->response->getHeaders());
        echo $output;
    }

    /**
     * Instantiates and invokes a middleware
     *
     * @param  string $middlewareClass
     * @return void
     */
    protected function invokeMiddleware(string $middlewareClass) : void
    {
        /**
         * @var MiddlewareInterface $middleware
         */
        $middleware = $this->container->get($middlewareClass);
        $requestAttributes = $this->request->getAttributes();

        // As an extra step if an action middleware is resolved, it should be invoked by the dispatcher.
        if (isset($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS])
            && $middleware instanceof CommonMiddleware\DispatcherMiddleware
        ) {
            /**
             * @var MiddlewareInterface $actionMiddleware
             */
            $actionMiddleware = $this->container
                ->get($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS]);
            $this->request = $this->request->withAttribute(
                ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE,
                $actionMiddleware
            );
        }

        $middleware($this->request, $this->response);
    }

    /**
     * Inject the Content-Length header if is not already present.
     *
     * NOTE: if there will be chunk content displayed, check if the response getSize counts the real size correctly
     *
     * @param  null|int $contentLength
     * @return void
     *
     * @codeCoverageIgnore - no putput for tests.
     */
    protected function injectContentLength(? int $contentLength) : void
    {
        $contentLength = intval($contentLength);

        if (!$this->response->hasHeader('Content-Length') && $contentLength > 0) {
            $this->response = $this->response->withHeader('Content-Length', (string) $contentLength);
        }
    }

    /**
     * Filter a header name to word case.
     *
     * @param  string $headerName
     * @return string
     */
    protected function filterHeaderName(string $headerName) : string
    {
        $filtered = str_replace('-', ' ', $headerName);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    /**
     * Sends the HTTP header.
     *
     * @return void
     *
     * @codeCoverageIgnore - vendor and core function calls
     */
    protected function sendHttpHeader() : void
    {
        $reasonPhrase = $this->response->getReasonPhrase();
        header(
            sprintf(
                'HTTP/%s %d%s',
                $this->response->getProtocolVersion(),
                $this->response->getStatusCode(),
                ($reasonPhrase ? ' '.$reasonPhrase : '')
            )
        );
    }

    /**
     * Sends out output headers.
     *
     * @param  array $headers
     * @return void
     *
     * @codeCoverageIgnore - vendor and core function calls in loop
     */
    protected function sendOutputHeaders(array $headers) : void
    {
        foreach ($headers as $headerName => $values) {
            $name  = $this->filterHeaderName($headerName);
            $first = true;

            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $first);
                $first = false;
            }
        }
    }
}
