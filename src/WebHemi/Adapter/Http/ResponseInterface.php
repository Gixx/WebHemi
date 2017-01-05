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

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Interface ResponseInterface.
 */
interface ResponseInterface extends PsrResponseInterface
{
    const STATUS_PROCESSING = 102;

    const STATUS_OK = 200;

    const STATUS_REDIRECT = 302;

    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_BAD_METHOD = 405;

    const STATUS_INTERNAL_SERVER_ERROR = 500;
}
