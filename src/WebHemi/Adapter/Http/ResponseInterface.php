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
declare(strict_types=1);

namespace WebHemi\Adapter\Http;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Interface ResponseInterface.
 */
interface ResponseInterface extends PsrResponseInterface
{
    public const STATUS_PROCESSING = 102;
    public const STATUS_OK = 200;
    public const STATUS_REDIRECT = 302;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_BAD_METHOD = 405;
    public const STATUS_INTERNAL_SERVER_ERROR = 500;
}
