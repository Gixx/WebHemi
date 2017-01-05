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
namespace WebHemi\Adapter\Http\GuzzleHttp;

use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;
use WebHemi\Adapter\Http\ServerRequestInterface;

/**
 * Class ServerRequest.
 */
class ServerRequest extends GuzzleServerRequest implements ServerRequestInterface
{
    // The only purpose of this extension is to be able to implement the WebHemi's ServerRequestInterface.
}
