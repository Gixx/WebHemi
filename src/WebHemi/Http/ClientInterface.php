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

namespace WebHemi\Http;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Get data.
     *
     * @param string $url
     * @param array $data
     * @return PsrResponseInterface
     */
    public function get(string $url, array $data) : PsrResponseInterface;

    /**
     * Posts data.
     *
     * @param  string $url
     * @param  array  $data
     * @return PsrResponseInterface
     */
    public function post(string $url, array $data) : PsrResponseInterface;
}
