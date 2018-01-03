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
use WebHemi\Renderer;

return [
    'renderer' => [
        'Admin' => [
            'filter' => [],
            'helper' => [
                Renderer\Helper\IsAllowedHelper::class
            ],
        ]
    ]
];
