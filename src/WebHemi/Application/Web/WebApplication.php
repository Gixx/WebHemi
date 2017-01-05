<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Application\Web;

use Exception;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Application\AbstractApplication;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Application\PipelineManager;
use WebHemi\Application\SessionManager;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class WebApplication.
 */
class WebApplication extends AbstractApplication
{
    /**
     * Starts the session.
     *
     * @codeCoverageIgnore - not testing session (yet)
     */
    private function initSession()
    {
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            return;
        }

        /** @var SessionManager $sessionManager */
        $sessionManager = $this->getContainer()->get(SessionManager::class);
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $this->getContainer()->get(EnvironmentManager::class);

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
    public function run()
    {
        // Start session.
        $this->initSession();

        /** @var HttpAdapterInterface $httpAdapter */
        $httpAdapter = $this->getContainer()->get(HttpAdapterInterface::class);
        /** @var PipelineManager $pipelineManager */
        $pipelineManager = $this->getContainer()->get(PipelineManager::class);
        /** @var EnvironmentManager $environmentManager */
        $environmentManager = $this->getContainer()->get(EnvironmentManager::class);

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

        /** @var ResponseInterface $response */
        $response = $httpAdapter->getResponse();
        /** @var string $middlewareClass */
        $middlewareClass = $pipelineManager->start();

        while ($middlewareClass !== null
            && $response->getStatusCode() == ResponseInterface::STATUS_PROCESSING
        ) {
            try {
                /** @var MiddlewareInterface $middleware */
                $middleware = $this->getContainer()->get($middlewareClass);
                $requestAttributes = $request->getAttributes();

                // As an extra step if the action middleware is resolved, it is invoked right before the dispatcher.
                // Only the container knows how to instantiate it in the right way, and the container must not be
                // injected into other classes. It seems like a hack but it is by purpose.
                if ($middleware instanceof DispatcherMiddleware
                    && isset($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS])
                ) {
                    /** @var MiddlewareInterface $actionMiddleware */
                    $actionMiddleware = $this->getContainer()
                        ->get($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS]);
                    $request = $request->withAttribute(
                        ServerRequestInterface::REQUEST_ATTR_ACTION_MIDDLEWARE,
                        $actionMiddleware
                    );
                }
                $response = $middleware($request, $response);
            } catch (Exception $exception) {
                $response = $response->withStatus(ResponseInterface::STATUS_INTERNAL_SERVER_ERROR);
                $request = $request->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION,
                    $exception
                );
            }

            $middlewareClass = $pipelineManager->next();
        };

        // If there was no error, we mark as ready for output.
        if ($response->getStatusCode() == ResponseInterface::STATUS_PROCESSING) {
            $response = $response->withStatus(ResponseInterface::STATUS_OK);
        }

        /** @var FinalMiddleware $finalMiddleware */
        $finalMiddleware = $this->getContainer()->get(FinalMiddleware::class);

        // Send out headers and content.
        $finalMiddleware($request, $response);
    }
}
