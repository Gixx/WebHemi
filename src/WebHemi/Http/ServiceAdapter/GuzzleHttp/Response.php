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

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use WebHemi\Http\ResponseInterface;

/**
 * Class Response.
 */
class Response extends GuzzleResponse implements ResponseInterface
{
    // The only purpose of this extension is to be able to implement the WebHemi's ResponseInterface.
}
