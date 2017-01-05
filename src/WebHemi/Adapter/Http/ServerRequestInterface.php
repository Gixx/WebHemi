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

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

/**
 * Interface ServerRequestInterface.
 */
interface ServerRequestInterface extends PsrServerRequestInterface
{
    const REQUEST_ATTR_RESOLVED_ACTION_CLASS = 'resolved_action_middleware';
    const REQUEST_ATTR_ACTION_MIDDLEWARE = 'action_middleware_instance';
    const REQUEST_ATTR_MIDDLEWARE_EXCEPTION = 'middleware_exception';
    const REQUEST_ATTR_ROUTING_PARAMETERS = 'routing_parameters';
    const REQUEST_ATTR_DISPATCH_TEMPLATE = 'dispatch_template';
    const REQUEST_ATTR_DISPATCH_DATA = 'dispatch_data';
    const REQUEST_ATTR_AUTHENTICATED_USER = 'authenticated_user';
}
