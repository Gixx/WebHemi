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
namespace WebHemi\Application\Web;

use InvalidArgumentException;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Router\RouterAdapterInterface;
use WebHemi\Application\AbstractApplication;
use WebHemi\Application\EnvironmentManager;
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
        $name = $this->getEnvironmentManager()->getSelectedApplication();
        $timeOut = 3600;
        $path = $this->getEnvironmentManager()->getSelectedApplicationUri();
        $domain = $this->getEnvironmentManager()->getApplicationDomain();
        $secure = $this->getEnvironmentManager()->isSecuredApplication();
        $httpOnly = true;

        $sessionManager->start($name, $timeOut, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Get ready to run the application: set final data for specific services.
     *
     * @codeCoverageIgnore - Check the EnvironmentManager and Container adapter tests.
     */
    private function prepareContainer()
    {
        // Register services according to the selected module.
        $this->getContainer()->registerModuleServices($this->getEnvironmentManager()->getSelectedModule());

        // Set proper arguments for the HTTP adapter.
        $this->getContainer()
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $this->getEnvironmentManager()->getEnvironmentData('GET')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $this->getEnvironmentManager()->getEnvironmentData('POST')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $this->getEnvironmentManager()->getEnvironmentData('SERVER')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $this->getEnvironmentManager()->getEnvironmentData('COOKIE')
            )
            ->setServiceArgument(
                HttpAdapterInterface::class,
                $this->getEnvironmentManager()->getEnvironmentData('FILES')
            );

        try {
            $theme = $this->getEnvironmentManager()
                ->getApplicationTemplateSettings($this->getEnvironmentManager()->getSelectedTheme());
            $themeResourcePath = $this->getEnvironmentManager()->getResourcePath();
        } catch (InvalidArgumentException $e) {
            $theme = $this->getEnvironmentManager()->getApplicationTemplateSettings(EnvironmentManager::DEFAULT_THEME);
            $themeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
        }

        // Set proper arguments for the renderer.
        $this->getContainer()
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $theme
            )
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $themeResourcePath
            )
            ->setServiceArgument(
                RendererAdapterInterface::class,
                $this->getEnvironmentManager()->getSelectedApplicationUri()
            );

        // Set proper arguments for the router.
        $this->getContainer()
            ->setServiceArgument(
                RouterAdapterInterface::class,
                $this->getEnvironmentManager()->getModuleRouteSettings()
            )
            ->setServiceArgument(
                RouterAdapterInterface::class,
                $this->getEnvironmentManager()->getSelectedApplicationUri()
            );
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
        // Inject parameters into services.
        $this->prepareContainer();

        /** @var HttpAdapterInterface $httpAdapter */
        $httpAdapter = $this->getContainer()->get(HttpAdapterInterface::class);
        /** @var ServerRequestInterface $request */
        $request = $httpAdapter->getRequest();
        /** @var ResponseInterface $response */
        $response = $httpAdapter->getResponse();
        $middlewareClass = $this->getPipelineManager()->start();

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
            } catch (\Exception $exception) {
                $response = $response->withStatus(ResponseInterface::STATUS_INTERNAL_SERVER_ERROR);
                $request = $request->withAttribute(
                    ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION,
                    $exception
                );
            }

            $middlewareClass = $this->getPipelineManager()->next();
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
