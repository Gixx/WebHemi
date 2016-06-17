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

use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Application\ApplicationInterface;
use WebHemi\Config\ConfigInterface;

/**
 * Class WebApplication.
 */
class WebApplication implements ApplicationInterface
{
    const MODULE_ADMIN = 'Admin';
    const MODULE_SITE = 'Website';

    /** @var DependencyInjectionAdapterInterface */
    private $container;
    /** @var ConfigInterface */
    private $config;
    /** @var array */
    private $server;
    /** @var array */
    private $get;
    /** @var array */
    private $post;
    /** @var array */
    private $cookie;
    /** @var array */
    private $files;
    ///** @var string */
    //private $defaultModule = self::MODULE_SITE;
    ///** @var string */
    //private $selectedModule;
    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     * @param ConfigInterface                     $config
     */
    public function __construct(DependencyInjectionAdapterInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns the Configuration.
     *
     * @return ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets application environments according to the super globals.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $cookie
     * @param array $files
     *
     * @return ApplicationInterface
     */
    public function setEnvironmentFromGlobals(array $get, array $post, array $server, array $cookie, array $files)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->files = $files;
    }

    /**
     * Runs the application. This is where the magic happens.
     * According tho the environment settings this must build up the middleware pipeline and execute it.
     *
     * Pre-Routing Middleware can be; priority < 0:
     *  - LockCheck - check if the client IP is banned > NULL|S403
     *  - Auth - if the user is not logged in, but there's a "Remember me" cookie, then logs in > NULL
     *
     * Routing Middleware is fixed; priority = 0:
     *  - A middleware that routes the incoming Request and delegates to the matched middleware. > NULL|S404|S405
     *    The RouteResult should be attached to the Request.
     *    If the Routing is not defined explicitly in the pipeline, then it will be injected with priority 0.
     *
     * Post-Routing Middleware can be; priority between 0 and 100:
     *  - Acl - checks if the given route is available for the client. Also checks the auth > NULL|S401|S403
     *  - CacheReader - checks if a suitable response body is cached. > NULL|S200
     *
     * Dispatcher Middleware is fixed; priority = 100:
     *  - A middleware which gets the corresponding Action middleware and applies it > NULL|S500
     *    If the Dispatcher is not defined explicitly in the pipeline, then it will be injected with priority 100.
     *    The Dispatcher should not set the response Status Code to 200 to let Post-Dispatchers to be called.
     *
     * Post-Dispatch Middleware can be; priority > 100:
     *  - CacheWriter - writes response body into DataStorage (DB, File etc.) > NULL
     *
     * Final Middleware is fixed:
     *  - This middleware behaves a bit differently. It cannot be ordered, it's always the last called middleware:
     *    - when the middleware pipeline reached its end (typically when the Status Code is empty)
     *    - when one item of the middleware pipeline returns with response (status code is set)
     *    - when during the pipeline process an Exception is thrown.
     *
     * When the middleware pipeline is finished the application prints the header and the output.
     *
     * @return void
     */
    public function run()
    {
        /*  -- Pseudo code for the implementation

            var request
            var response
            var pipeline
            var finalMiddleware

            FOR var middleware IN pipeline
                response = CALL middleware WITH request, response

                IF response.header.statusCode IS NOT NULL THEN
                    BREAK
                END IF
            END FOR

            response = CALL finalMiddleware WITH request, response

            SEND response.header
            PRINT response.body

            EXIT

         */


        echo '<h1>Hello world!</h1>';
    }
}
