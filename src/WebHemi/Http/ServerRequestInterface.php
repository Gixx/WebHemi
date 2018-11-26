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

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

/**
 * Interface ServerRequestInterface.
 */
interface ServerRequestInterface extends PsrServerRequestInterface
{
    public const REQUEST_ATTR_RESOLVED_ACTION_CLASS = 'resolved_action_middleware';
    public const REQUEST_ATTR_ACTION_MIDDLEWARE = 'action_middleware_instance';
    public const REQUEST_ATTR_MIDDLEWARE_EXCEPTION = 'middleware_exception';
    public const REQUEST_ATTR_ROUTING_PARAMETERS = 'routing_parameters';
    public const REQUEST_ATTR_ROUTING_RESOURCE = 'routing_resource';
    public const REQUEST_ATTR_DISPATCH_TEMPLATE = 'dispatch_template';
    public const REQUEST_ATTR_DISPATCH_DATA = 'dispatch_data';
    public const REQUEST_ATTR_AUTHENTICATED_USER = 'authenticated_user';
    public const REQUEST_ATTR_AUTHENTICATED_USER_META = 'authenticated_user_meta';
    public const REQUEST_ATTR_APPLICATION_URI = 'application_uri';

    /**
     * Checks if it is an XML HTTP Request (Ajax) or not.
     *
     * @return bool
     */
    public function isXmlHttpRequest() : bool;
}
