<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Adapter\Http;

use WebHemi\Application\EnvironmentManager;

/**
 * Interface AdapterInterface.
 */
interface HttpAdapterInterface
{
    /**
     * AdapterInterface constructor.
     *
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager);

    /**
     * Returns the HTTP request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest();

    /**
     * Returns the response being sent.
     *
     * @return ResponseInterface
     */
    public function getResponse();
}
