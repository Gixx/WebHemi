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
namespace WebHemi\Adapter\Http;

/**
 * Interface AdapterInterface.
 */
interface HttpAdapterInterface
{
    /**
     * AdapterInterface constructor.
     *
     * @param array $get
     * @param array $post
     * @param array $server
     * @param array $cookie
     * @param array $files
     */
    public function __construct(array $get, array $post, array $server, array $cookie, array $files);

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
