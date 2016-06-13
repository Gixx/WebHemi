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
use WebHemi\Application\ConfigInterface;

/**
 * Class WebApplication.
 */
class WebApplication implements ApplicationInterface
{
    /** @var DependencyInjectionAdapterInterface */
    private $container;
    /** @var ConfigInterface */
    private $config;
    /** @var array */
    protected $server;
    /** @var array */
    protected $get;
    /** @var array */
    protected $post;
    /** @var array */
    protected $cookie;
    /** @var array */
    protected $files;
    /** @var string */
    protected $selectedModule;

    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface  $container
     * @param \WebHemi\Application\ConfigInterface $config
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

        // TODO return data only for the 'core' and the selected 'module'
    }

    /**
     * Sets application environments according to the super globals. This is typically good to choose between
     * application modules, like 'Website' or 'Admin'.
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

        // TODO create module selection
    }

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Routing and the Dispatch.
     *
     * @return void
     */
    public function run()
    {
        // TODO create request, response, DI, template, routing, middleware pipelines
        // TODO share Config in DI

        echo '<h1>Hello world!</h1>';
    }
}
