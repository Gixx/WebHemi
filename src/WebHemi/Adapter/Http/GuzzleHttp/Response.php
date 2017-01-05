<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Adapter\Http\GuzzleHttp;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use WebHemi\Adapter\Http\ResponseInterface;

/**
 * Class Response.
 */
class Response extends GuzzleResponse implements ResponseInterface
{
    // The only purpose of this extension is to be able to implement the WebHemi's ResponseInterface.
}
