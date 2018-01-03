<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;
use WebHemi\Http\ServerRequestInterface;

/**
 * Class ServerRequest.
 */
class ServerRequest extends GuzzleServerRequest implements ServerRequestInterface
{
    /**
     * Checks if it is an XML HTTP Request (Ajax) or not.
     *
     * @return bool
     */
    public function isXmlHttpRequest() : bool
    {
        $serverParams = $this->getServerParams();

        return (
            isset($serverParams['HTTP_X_REQUESTED_WITH'])
            && $serverParams['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
        );
    }
}
