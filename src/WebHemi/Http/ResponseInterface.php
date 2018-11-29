<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Http;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Interface ResponseInterface.
 */
interface ResponseInterface extends PsrResponseInterface
{
    public const STATUS_PROCESSING = 102;
    public const STATUS_OK = 200;
    public const STATUS_REDIRECT = 302;
    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_BAD_METHOD = 405;
    public const STATUS_INTERNAL_SERVER_ERROR = 500;
    public const STATUS_NOT_IMPLEMENTED = 501;
}
