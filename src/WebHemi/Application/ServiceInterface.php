<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
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
     * @return ServiceInterface
     */
    public function initSession() : ServiceInterface;

    /**
     * Initializes the I18n Service.
     *
     * @return ServiceInterface
     */
    public function initInternationalization() : ServiceInterface;

    /**
     * Runs the application. This is where the magic happens.
     * For example for a web application this initializes the Request and Response objects, builds the middleware
     * pipeline, applies the Router and the Dispatcher.
     *
     * @return ServiceInterface
     */
    public function run() : ServiceInterface;

    /**
     * Renders the response body and sends it to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    public function renderOutput() : void;

    /**
     * Sends the response body to the client.
     *
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    public function sendOutput() : void;
}
