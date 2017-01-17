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
    public function getRequest() : ServerRequestInterface;

    /**
     * Returns the response being sent.
     *
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface;
}
