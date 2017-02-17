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

namespace WebHemi\Application;

use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;

/**
 * Interface ApplicationInterface.
 */
interface ApplicationInterface
{
    /**
     * ApplicationInterface constructor.
     *
     * @param DependencyInjectionAdapterInterface $container
     */
    public function __construct(DependencyInjectionAdapterInterface $container);

    /**
     * Returns the DI Adapter instance.
     *
     * @return DependencyInjectionAdapterInterface
     */
    public function getContainer() : DependencyInjectionAdapterInterface;

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Router and the Dispatch.
     *
     * @return void
     */
    public function run() : void;
}
