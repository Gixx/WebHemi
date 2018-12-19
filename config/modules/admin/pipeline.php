<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

use WebHemi\Middleware\Security\AclMiddleware;
use WebHemi\Middleware\Security\AccessLogMiddleware;
use WebHemi\Middleware\Security\CSRFMiddleware;

return [
    'middleware_pipeline' => [
        'Admin' => [
            ['service' => AclMiddleware::class, 'priority' => 10],
            ['service' => CSRFMiddleware::class, 'priority' => 11],
            ['service' => AccessLogMiddleware::class, 'priority' => 12],
        ],
    ],
];
