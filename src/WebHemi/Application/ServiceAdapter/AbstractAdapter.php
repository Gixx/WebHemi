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

namespace WebHemi\Application\ServiceAdapter;

use Throwable;
use WebHemi\Application\ServiceInterface;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ServiceInterface as HttpInterface;
use WebHemi\I18n\ServiceInterface as I18nInterface;

/**
 * Class AbstractAdapter
 */
abstract class AbstractAdapter implements ServiceInterface
{
    /**
     * @var Throwable
     */
    public $error;
    /**
     * @var DependencyInjectionInterface
     */
    protected $container;
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @var I18nInterface
     */
    protected $i18n;

    /**
     * ServiceAdapter constructor.
     *
     * @param DependencyInjectionInterface $container
     */
    public function __construct(DependencyInjectionInterface $container)
    {
        $this->container = $container;
        /**
         * @var HttpInterface $httpAdapter
         */
        $httpAdapter = $this->container->get(HttpInterface::class);
        /**
         * @var ServerRequestInterface $request
         */
        $this->request = $httpAdapter->getRequest();
        /**
         * @var ResponseInterface $response
         */
        $this->response = $httpAdapter->getResponse();

        $this->setApplicationUri();
    }

    /**
     * Sets the application URI into the request object.
     */
    protected function setApplicationUri()
    {
        /**
         * @var EnvironmentInterface $environmentManager
         */
        $environmentManager = $this->container->get(EnvironmentInterface::class);
        $applicationUri = rtrim($environmentManager->getSelectedApplicationUri(), '/').'/';
        $this->request = $this->request
            ->withAttribute(ServerRequestInterface::REQUEST_ATTR_APPLICATION_URI, $applicationUri);
    }

    /**
     * Starts the session.
     *
     * @return ServiceInterface
     */
    abstract public function initSession() : ServiceInterface;

    /**
     * Initializes the I18n Service.
     *
     * @return ServiceInterface
     */
    public function initInternationalization() : ServiceInterface
    {
        /** @var I18nInterface $instance */
        $instance = $this->container->get(I18nInterface::class);
        $this->i18n = $instance;

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
    abstract public function run() : ServiceInterface;

    /**
     * Renders the response body and sends it to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    abstract public function renderOutput() : void;

    /**
     * Sends the response body to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    abstract public function sendOutput() : void;
}
