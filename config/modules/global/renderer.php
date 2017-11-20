<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
use WebHemi\Renderer;

return [
    'renderer' => [
        'Global' => [
            'filter' => [
                Renderer\Filter\MarkDownFilter::class,
                Renderer\Filter\TagParserFilter::class,
                Renderer\Filter\TranslateFilter::class
            ],
            'helper' => [
                Renderer\Helper\DefinedHelper::class,
                Renderer\Helper\FileExistsHelper::class,
                Renderer\Helper\GetStatHelper::class,
            ],
        ]
    ]
];
