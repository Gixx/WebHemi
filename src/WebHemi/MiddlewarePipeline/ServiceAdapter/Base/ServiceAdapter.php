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

namespace WebHemi\MiddlewarePipeline\ServiceAdapter\Base;

use WebHemi\Middleware\Common;
use WebHemi\MiddlewarePipeline\ServiceAdapter\AbstractAdapter;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractAdapter
{
    /**
     * Initialize the listing properties (priorityList, pipelineList, keyMiddlewareList).
     *
     * @return void
     */
    protected function initProperties() : void
    {
        $this->keyMiddlewareList = [
            Common\RoutingMiddleware::class,
            Common\DispatcherMiddleware::class,
            Common\FinalMiddleware::class
        ];

        // The FinalMiddleware should not be part of the queue.
        $this->priorityList = [
            0   => [Common\RoutingMiddleware::class],
            100 => [Common\DispatcherMiddleware::class]
        ];

        $this->pipelineList = [
            Common\RoutingMiddleware::class,
            Common\DispatcherMiddleware::class,
        ];
    }
}
