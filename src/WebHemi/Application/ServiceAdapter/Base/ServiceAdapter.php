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

namespace WebHemi\Application\ServiceAdapter\Base;

use Throwable;
use WebHemi\Application\ServiceInterface;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ServiceInterface as HttpInterface;
use WebHemi\Middleware\Common as CommonMiddleware;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\MiddlewarePipeline\ServiceInterface as PipelineInterface;
use WebHemi\Session\ServiceInterface as SessionInterface;

/**
 * Class ServiceAdapter
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var DependencyInjectionInterface */
    private $container;

    /**
     * ServiceAdapter constructor.
     *
     * @param DependencyInjectionInterface $container
     */
    public function __construct(DependencyInjectionInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Starts the session.
     *
     * @return void
     *
     * @codeCoverageIgnore - not testing session (yet)
     */
    private function initSession() : void
    {
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            return;
        }

        /** @var SessionInterface $sessionManager */
        $sessionManager = $this->container->get(SessionInterface::class);
        /** @var EnvironmentInterface $environmentManager */
        $environmentManager = $this->container->get(EnvironmentInterface::class);

        $name = $environmentManager->getSelectedApplication();
        $timeOut = 3600;
        $path = $environmentManager->getSelectedApplicationUri();
        $domain = $environmentManager->getApplicationDomain();
        $secure = $environmentManager->isSecuredApplication();
        $httpOnly = true;

        $sessionManager->start($name, $timeOut, $path, $domain, $secure, $httpOnly);
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
     * @return void
     */
    public function run() : void
    {
        // Start session.
        $this->initSession();

        /** @var PipelineInterface $pipelineManager */
        $pipelineManager = $this->container->get(PipelineInterface::class);
        $request = $this->getRequest();
        $response = $this->getResponse();

        /** @var string $middlewareClass */
        $middlewareClass = $pipelineManager->start();

        while ($middlewareClass !== null
            && $response->getStatusCode() == ResponseInterface::STATUS_PROCESSING
        ) {
            $this->invokeMiddleware($middlewareClass, $request, $response);
            $middlewareClass = $pipelineManager->next();
        };

        // If there was no error, we mark as ready for output.
        if ($response->getStatusCode() == ResponseInterface::STATUS_PROCESSING) {
            $response = $response->withStatus(ResponseInterface::STATUS_OK);
        }

        /** @var CommonMiddleware\FinalMiddleware $finalMiddleware */
        $finalMiddleware = $this->container->get(CommonMiddleware\FinalMiddleware::class);

        // Send out headers and content.
        $finalMiddleware($request, $response);
    }

    /**
     * Gets the Request object.
     *
     * @return ServerRequestInterface
     */
    private function getRequest()
    {
        /** @var HttpInterface $httpAdapter */
        $httpAdapter = $this->container->get(HttpInterface::class);
        /** @var EnvironmentInterface $environmentManager */
        $environmentManager = $this->container->get(EnvironmentInterface::class);

        /** @var ServerRequestInterface $request */
        $request = $httpAdapter->getRequest()
            ->withAttribute(
                ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA,
                [
                    'selected_module' => $environmentManager->getSelectedModule(),
                    'application_domain' => 'http'.($environmentManager->isSecuredApplication() ? 's' : '')
                        .'://'
                        .$environmentManager->getApplicationDomain()
                ]
            );

        return $request;
    }

    /**
     * Gets the Response object.
     *
     * @return ResponseInterface
     */
    private function getResponse()
    {
        /** @var HttpInterface $httpAdapter */
        $httpAdapter = $this->container->get(HttpInterface::class);

        /** @var ResponseInterface $response */
        return $httpAdapter->getResponse();
    }

    /**
     * Instantiates and invokes a middleware
     *
     * @param string $middlewareClass
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    private function invokeMiddleware(
        string $middlewareClass,
        ServerRequestInterface&$request,
        ResponseInterface&$response
    ) : void {
        try {
            /** @var MiddlewareInterface $middleware */
            $middleware = $this->container->get($middlewareClass);
            $requestAttributes = $request->getAttributes();

            // As an extra step if an action middleware is resolved, it should be invoked by the dispatcher.
            if ($middleware instanceof CommonMiddleware\DispatcherMiddleware
                && isset($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS])
            ) {
                /** @var MiddlewareInterface $actionMiddleware */
                $actionMiddleware = $this->container
                    ->get($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS]);
                $request = $request->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE,
                    $actionMiddleware
                );
            }

            $middleware($request, $response);
        } catch (Throwable $exception) {
            $code = ResponseInterface::STATUS_INTERNAL_SERVER_ERROR;

            if (in_array($exception->getCode(), [403, 404])) {
                $code = $exception->getCode();
            }

            $response = $response->withStatus($code);
            $request = $request->withAttribute(
                ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION,
                $exception
            );
        }
    }
}
