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
use WebHemi\Renderer;

return [
    'renderer' => [
        'Admin' => [
            'filter' => [],
            'helper' => [
                Renderer\Helper\IsAllowedHelper::class,
                Renderer\Helper\GetCurrentUserHelper::class
            ],
        ]
    ]
];
