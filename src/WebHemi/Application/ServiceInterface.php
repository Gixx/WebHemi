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

namespace WebHemi\Application;

use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param DependencyInjectionInterface $container
     */
    public function __construct(DependencyInjectionInterface $container);

    /**
     * Starts the session.
     *
     * @return void
     */
    public function initSession() : void;

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Router and the Dispatch.
     *
     * @return void
     */
    public function run() : void;
}
