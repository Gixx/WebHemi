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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Application\ApplicationInterface;
use WebHemi\Config\ConfigInterface;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\MiddlewareInvokerInterface;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\Pipeline\MiddlewarePipelineInterface;

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
    /** @var MiddlewarePipelineInterface */
    private $pipeline;
    /** @var array  */
    private $environmentData = [
        'GET'    => [],
        'POST'   => [],
        'SERVER' => [],
        'COOKIE' => [],
        'FILES'  => [],
    ];
    /** @var string */
    private $selectedModule = self::MODULE_SITE;

    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     * @param ConfigInterface                     $config
     * @param MiddlewarePipelineInterface         $pipeline
     */
    public function __construct(
        DependencyInjectionAdapterInterface $container,
        ConfigInterface $config,
        MiddlewarePipelineInterface $pipeline
    ) {
        $this->container = $container;
        $this->config = $config;
        $this->pipeline = $pipeline;
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
     * @param string $key
     * @param array  $data
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setEnvironmentData($key, array $data)
    {
        if (!isset($this->environmentData[$key])) {
            throw new InvalidArgumentException(sprintf('The key "%s" is not a valid super global key.', $key));
        }

        $this->environmentData[$key] = $data;

        return $this;
    }

    /**
     * Get ready to run the application: set final data for specific services.
     */
    private function prepare()
    {
        $this->container
            ->setServiceArgument(HttpAdapterInterface::class, $this->environmentData['SERVER'])
            ->setServiceArgument(HttpAdapterInterface::class, $this->environmentData['GET'])
            ->setServiceArgument(HttpAdapterInterface::class, $this->environmentData['POST'])
            ->setServiceArgument(HttpAdapterInterface::class, $this->environmentData['COOKIE'])
            ->setServiceArgument(HttpAdapterInterface::class, $this->environmentData['FILES']);

        $moduleConfig = $this->config->get('modules/' . $this->selectedModule, ConfigInterface::CONFIG_AS_OBJECT);
        $this->container
            ->setServiceArgument(FinalMiddleware::class, $moduleConfig);

        /** @var array $pipelineConfig */
        $pipelineConfig = (array)$this->config->get('middleware_pipeline');

        foreach ($pipelineConfig as $middlewareData) {
            if (!isset($middlewareData['priority'])) {
                $middlewareData['priority'] = 50;
            }
            $this->pipeline->queueMiddleware($middlewareData['class'], $middlewareData['priority']);
        }
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
        $this->prepare();

        /** @var HttpAdapterInterface $httpAdapter */
        $httpAdapter = $this->getContainer()->get(HttpAdapterInterface::class);
        /** @var ServerRequestInterface $request */
        $request = $httpAdapter->getRequest();
        /** @var ResponseInterface $response */
        $response = $httpAdapter->getResponse();

        $middlewareClass = $this->pipeline->start();

        while ($middlewareClass !== null) {
            try {
                /** @var MiddlewareInterface $middleware */
                $middleware = $this->container->get($middlewareClass);

                $requestAttributes = $request->getAttributes();
                // As an extra step if the action middleware is resolved, it is invoked right before the dispatcher.
                if ($middleware instanceof DispatcherMiddleware
                    && isset($requestAttributes['resolvedActionMiddleware'])
                ) {
                    /** @var MiddlewareInterface $actionMiddleware */
                    $actionMiddleware = $this->container->get($requestAttributes['resolvedActionMiddleware']);
                    $response = $actionMiddleware($request, $response);
                }

                $response = $middleware($request, $response);
            } catch (\Exception $exception) {
                $response = $response->withStatus(500);
                $request = $request->withAttribute('exception', $exception);
            }

            if ($response->getStatusCode() != 102) {
                break;
            }

            $middlewareClass = $this->pipeline->next();
        };

        // If there was no error, we mark as ready for output.
        if ($response->getStatusCode() == 102) {
            $response = $response->withStatus(200);
        }

        /** @var FinalMiddleware $finalMiddleware */
        $finalMiddleware = $this->container->get(FinalMiddleware::class);

        // Send out headers and content.
        $finalMiddleware($request, $response);
    }
}
