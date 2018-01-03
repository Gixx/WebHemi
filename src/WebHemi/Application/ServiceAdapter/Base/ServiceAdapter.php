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

namespace WebHemi\Application\ServiceAdapter\Base;

use WebHemi\Application\ServiceAdapter\AbstractAdapter;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ServiceInterface as HttpInterface;
use WebHemi\MiddlewarePipeline\ServiceInterface as PipelineInterface;
use WebHemi\Middleware\Common as CommonMiddleware;
use WebHemi\Session\ServiceInterface as SessionInterface;

/**
 * Class ServiceAdapter
 */
class ServiceAdapter extends AbstractAdapter
{
    /**
     * Starts the session.
     *
     * @return void
     *
     * @codeCoverageIgnore - not testing session (yet)
     */
    public function initSession() : void
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
    protected function getRequest() : ServerRequestInterface
    {
        /** @var HttpInterface $httpAdapter */
        $httpAdapter = $this->container->get(HttpInterface::class);

        /** @var ServerRequestInterface $request */
        return $httpAdapter->getRequest();
    }

    /**
     * Gets the Response object.
     *
     * @return ResponseInterface
     */
    protected function getResponse() : ResponseInterface
    {
        /** @var HttpInterface $httpAdapter */
        $httpAdapter = $this->container->get(HttpInterface::class);

        /** @var ResponseInterface $response */
        return $httpAdapter->getResponse();
    }
}
